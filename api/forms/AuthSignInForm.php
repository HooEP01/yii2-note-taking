<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\forms;

use common\base\enum\AuthIdentityType;
use common\base\enum\FirebaseSignInProvider;
use common\base\enum\SocialChannel;
use common\base\enum\UserStatus;
use common\base\helpers\ArrayHelper;
use common\base\helpers\MobilePhoneHelper;
use common\base\helpers\StringHelper;
use common\base\traits\RuntimeCache;
use common\models\User;
use common\models\UserEmail;
use common\models\UserPhone;
use common\models\UserSocial;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\Model;
use Yii;

/**
 * Class AuthSignInForm
 * @property User $user
 * @package api\forms
 */
class AuthSignInForm extends Model
{
    use RuntimeCache;

    const SCENARIO_FIREBASE = 'firebase';
    const SCENARIO_PASSWORD = 'password';

    /** @var string */
    public $idToken;
    /** @var string */
    public $username;
    /** @var string */
    public $password;
    /** @var string */
    public $referrerCode;

    public $errorMessage = '';
    public $isSignup = false;

    /**
     * @var User
     */
    private $_user;

    private $_identities = [];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'password', 'idToken', 'referrerCode'], 'trim'],
            [['username', 'password', 'idToken'], 'required'],
            [['referrerCode'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['referrerCode'],
            self::SCENARIO_PASSWORD => ['username', 'password'],
            self::SCENARIO_FIREBASE => ['idToken'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('form', 'sign_in.username'),
            'password' => Yii::t('form', 'sign_in.password'),
            'idToken' => Yii::t('form', 'sign_in.idToken'),
            'referrerCode' => Yii::t('form', 'sign_in.referrerCode'),
        ];
    }

    /**
     * @return User
     * @throws InvalidCallException
     */
    public function getUser()
    {
        if (!isset($this->_user)) {
            throw new InvalidCallException('$user must be set!');
        }
        return $this->_user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->getScenario() === self::SCENARIO_FIREBASE) {
            return $this->processIdToken();
        }

        if ($this->getScenario() === self::SCENARIO_PASSWORD) {
            if (($user = $this->findUser()) !== null && $user->validatePassword($this->password)) {
                $this->setUser($user);
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     * @throws AuthException
     * @throws FirebaseException
     * @throws InvalidConfigException
     */
    protected function processIdToken()
    {
        $data = ['data' => Yii::$app->request->getBodyParams()];
        if (!$this->load($data, 'data')) {
            $this->errorMessage = 'Invalid Token [Firebase idToken]';
            return false;
        }

        if (!$this->validate()) {
            $errors = $this->getErrorSummary(true);
            $this->errorMessage = $errors[0];
            return false;
        }

        if (!$this->resolveIdentitiesFromIdToken()) {
            return false;
        }

        $duration = (int) ArrayHelper::getValue(Yii::$app->params, 'user.cookies.duration', 7200);
        $emailAddress = ArrayHelper::getValue($this->_identities, [AuthIdentityType::EMAIL]);
        if (!empty($emailAddress)) {
            $userEmail = UserEmail::find()->address($emailAddress)->active()->limit(1)->one();
            if ($userEmail === null) {
                if ($this->processNewUserSignUp()) {
                    return Yii::$app->user->login($this->user, $duration);
                }
            } else {
                if ($this->processExistingUserSignIn($userEmail)) {
                    //-- sign in user for 120 minutes
                    return Yii::$app->user->login($this->user, $duration);
                }
            }
        }

        $mobilePhone = ArrayHelper::getValue($this->_identities, [AuthIdentityType::PHONE]);
        if (!empty($mobilePhone)) {
            $userPhone = UserPhone::find()->complete($mobilePhone)->active()->limit(1)->one();
            if ($userPhone === null) {
                if ($this->processNewUserSignUp()) {
                    return Yii::$app->user->login($this->user, $duration);
                }
            } else {
                if ($this->processExistingUserSignIn($userPhone)) {
                    //-- sign in user for 120 minutes
                    return Yii::$app->user->login($this->user, $duration);
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function resolveIdentitiesFromIdToken()
    {
        try {
            $auth = Yii::$app->firebase->getAuth();
            $result = $auth->verifyIdToken($this->idToken, true);

            $claims = $result->claims()->all();
            Yii::debug($claims);

            $displayName = ArrayHelper::getValue($claims, ['name']);
            if (!empty($displayName)) {
                $this->_identities[AuthIdentityType::NAME] = $displayName;
            }

            $avatarImageSrc = ArrayHelper::getValue($claims, ['picture']);
            if (!empty($avatarImageSrc)) {
                $this->_identities[AuthIdentityType::AVATAR] = $avatarImageSrc;
            }

            $firebase = (array) ArrayHelper::getValue($claims, ['firebase']);
            $signInProvider = ArrayHelper::getValue($firebase, ['sign_in_provider'], '-');
            if (!FirebaseSignInProvider::isValid($signInProvider)) {
                $this->errorMessage = 'Invalid Sign In Provider: ' . $signInProvider . ' !!';
                return false;
            }

            $emailAddress = (array) ArrayHelper::getValue($firebase, ['identities', 'email']);
            if (!empty($emailAddress)) {
                $this->_identities[AuthIdentityType::EMAIL] = current($emailAddress);
            }

            $mobilePhone = (array) ArrayHelper::getValue($firebase, ['identities', 'phone']);
            if (!empty($mobilePhone)) {
                $this->_identities[AuthIdentityType::PHONE] = current($mobilePhone);
            }

            $googleId = (array) ArrayHelper::getValue($firebase, ['identities', 'google.com']);
            if (!empty($googleId)) {
                $this->_identities[AuthIdentityType::GOOGLE] = current($googleId);
            }

            $facebookId = (array) ArrayHelper::getValue($firebase, ['identities', 'facebook.com']);
            if (!empty($facebookId)) {
                $this->_identities[AuthIdentityType::FACEBOOK] = current($facebookId);
            }

            if (empty($emailAddress) && empty($mobilePhone)) {
                $this->errorMessage = 'Please register with Email or Mobile Phone !!';
                return false;
            }
        } catch (FailedToSignIn $e) {
            Yii::error($e->getMessage());
            $this->errorMessage = 'Failed to sign in with idToken !';
        } catch(\Exception $e) {
            $this->errorMessage = $e->getMessage();
            Yii::error($e->getMessage());
            Yii::error($e->getTraceAsString());
        }

        return !empty($this->_identities);
    }

    /**
     * @param User|UserEmail|UserPhone $model
     * @return bool
     */
    protected function processExistingUserSignIn($model)
    {
        $valid = true;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model instanceof UserEmail || $model instanceof UserPhone) {
                $this->setUser($model->user);
                $model->isVerified = true;
                $valid = $valid && $model->save();
            } elseif ($model instanceof User) {
                $this->setUser($model);
            } else {
                throw new InvalidArgumentException('Invalid Model [E001] !!');
            }

            $this->user->status = UserStatus::ACTIVE;
            $valid = $valid && $this->user->save();
            $valid ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $valid = false;
            $transaction->rollBack();
            Yii::error($e);
        }

        return $valid;
    }

    /**
     * @return boolean
     */
    protected function processNewUserSignUp()
    {
        $valid = true;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = $this->createNewUser();
            $user->setPassword(StringHelper::randomString(12));
            $user->generatePasswordResetToken();
            $valid = $valid && $user->save();
            if ($user->hasErrors()) {
                Yii::error($user->errors);
            }

            if (!$valid) {
                $transaction->rollBack();
                return false;
            }

            $emailAddress = ArrayHelper::getValue($this->_identities, [AuthIdentityType::EMAIL]);
            Yii::error($this->_identities);
            if (!empty($emailAddress)) {
                $userEmail = new UserEmail();
                $userEmail->userId = $user->id;
                $userEmail->address = $emailAddress;
                $userEmail->isVerified = true;
                $valid = $valid && $userEmail->save();
                if ($valid) {
                    $user->emailId = $userEmail->id;
                    $valid = $user->save(false, ['emailId']);
                } elseif ($userEmail->hasErrors()) {
                    $this->errorMessage = 'Failed to setup email...';
                    Yii::error($userEmail->errors);
                }
            }

            $mobilePhone = ArrayHelper::getValue($this->_identities, [AuthIdentityType::PHONE]);
            if (!empty($mobilePhone)) {
                $mobile = MobilePhoneHelper::resolve($mobilePhone);
                $isValidMobile = ArrayHelper::getValue($mobile, ['attribute', 'is_valid'], false);
                if ($isValidMobile) {
                    $userPhone = new UserPhone();
                    $userPhone->userId = $user->id;
                    $userPhone->prefix = ArrayHelper::getValue($mobile, ['mobile', 'prefix']);
                    $userPhone->number = ArrayHelper::getValue($mobile, ['mobile', 'number']);
                    $userPhone->complete = ArrayHelper::getValue($mobile, ['mobile', 'complete']);
                    $userPhone->isVerified = true;
                    $valid = $valid && $userPhone->save();
                    if ($valid) {
                        $user->phoneId = $userPhone->id;
                        $valid = $user->save(false, ['phoneId']);
                    } elseif ($userPhone->hasErrors()) {
                        $this->errorMessage = 'Failed to setup phone...';
                        Yii::error($userPhone->errors);
                    }
                }
            }

            $googleId = ArrayHelper::getValue($this->_identities, [AuthIdentityType::GOOGLE]);
            if (!empty($googleId)) {
                $userSocialGoogle = new UserSocial();
                $userSocialGoogle->userId = $user->id;
                $userSocialGoogle->channel = SocialChannel::GOOGLE;
                $userSocialGoogle->channelId = $googleId;
                $userSocialGoogle->isVerified = true;

                $displayName = ArrayHelper::getValue($this->_identities, [AuthIdentityType::NAME]);
                if (!empty($displayName)) {
                    $userSocialGoogle->channelName = $displayName;
                }

                $avatarImageSrc = ArrayHelper::getValue($this->_identities, [AuthIdentityType::AVATAR]);
                if (!empty($avatarImageSrc)) {
                    $userSocialGoogle->channelAvatarImageSrc = $avatarImageSrc;
                }

                $valid = $valid && $userSocialGoogle->save();
                if ($userSocialGoogle->hasErrors()) {
                    $this->errorMessage = 'Failed to link Google...';
                    Yii::error($userSocialGoogle->errors);
                }
            }

            $facebookId = ArrayHelper::getValue($this->_identities, [AuthIdentityType::FACEBOOK]);
            if (!empty($facebookId)) {
                $userSocialFacebook = new UserSocial();
                $userSocialFacebook->userId = $user->id;
                $userSocialFacebook->channel = SocialChannel::FACEBOOK;
                $userSocialFacebook->channelId = $facebookId;
                $userSocialFacebook->isVerified = true;

                $displayName = ArrayHelper::getValue($this->_identities, [AuthIdentityType::NAME]);
                if (!empty($displayName)) {
                    $userSocialFacebook->channelName = $displayName;
                }

                $avatarImageSrc = ArrayHelper::getValue($this->_identities, [AuthIdentityType::AVATAR]);
                if (!empty($avatarImageSrc)) {
                    $userSocialFacebook->channelAvatarImageSrc = $avatarImageSrc;
                }

                $valid = $valid && $userSocialFacebook->save();
                if ($userSocialFacebook->hasErrors()) {
                    $this->errorMessage = 'Failed to link Facebook...';
                    Yii::error($userSocialFacebook->errors);
                }
            }

            if ($valid) {
                $this->isSignup = true;
                $this->setUser($user);
            }

            $valid ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $valid = false;
            $transaction->rollBack();
            Yii::error($e);
        }

        return $valid;
    }

    /**
     * @return User
     * @throws Exception
     */
    protected function createNewUser()
    {
        $user = User::factory();
        $displayName = ArrayHelper::getValue($this->_identities, [AuthIdentityType::NAME]);
        if (!empty($displayName)) {
            $user->displayName = $displayName;
        }

        if (!empty($this->referrerCode) && $referrer = User::findIdentityByReferrerCode($this->referrerCode)) {
            $user->referrerUserId = $referrer->id;
        }

        $this->setUser($user);
        return $user;
    }

    /**
     * @return mixed
     */
    protected function findUser()
    {
        if (($user = User::findIdentityByUsername($this->username)) !== null) {
            return $user;
        }

        if (($user = User::findIdentityByEmail($this->username)) !== null) {
            return $user;
        }

        if (($user = User::findIdentityByPhone($this->username)) !== null) {
            return $user;
        }

        return null;
    }
}