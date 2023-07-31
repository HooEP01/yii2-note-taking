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
use common\base\helpers\ArrayHelper;
use common\base\helpers\MobilePhoneHelper;
use common\base\traits\RuntimeCache;
use common\models\User;
use common\models\UserEmail;
use common\models\UserPhone;
use common\models\UserSocial;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class IdentityBindForm
 * @property User $user
 * @package api\forms
 */
class IdentityBindForm extends BaseUserForm
{
    use RuntimeCache;

    /** @var string */
    public $type;
    /** @var string */
    public $idToken;
    /** @var string */
    public $errorMessage = '';
    /** @var array  */
    private $_identities = [];

    public function init()
    {
        parent::init();

        if (!isset($this->_user)) {
            throw new InvalidCallException('$user must be set!');
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['idToken'], 'trim'],
            [['idToken', 'type'], 'required'],
            [['type'], 'in', 'range' => AuthIdentityType::bindable()],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('form', 'identity_bind.type'),
            'idToken' => Yii::t('form', 'identity_bind.idToken'),
        ];
    }

    /**
     * @return bool
     */
    public function process()
    {
        if (!$this->validate()) {
            $errors = $this->getErrorSummary(true);
            $this->errorMessage = $errors[0];
            return false;
        }

        if (!$this->resolveIdentitiesFromIdToken()) {
            return false;
        }

        if ($this->type === AuthIdentityType::PHONE) {
            return $this->processPhone();
        } elseif ($this->type === AuthIdentityType::FACEBOOK) {
            return $this->processFacebook();
        } elseif ($this->type === AuthIdentityType::GOOGLE) {
            return $this->processGoogle();
        }

        return false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function processPhone()
    {
        $mobilePhone = ArrayHelper::getValue($this->_identities, [AuthIdentityType::PHONE]);
        if (empty($mobilePhone)) {
            throw new InvalidCallException('Invalid token, the value given is not a phone token !');
        }

        $userPhone = UserPhone::find()->complete($mobilePhone)->active()->limit(1)->one();
        if ($userPhone === null) {
            $mobile = MobilePhoneHelper::resolve($mobilePhone);
            $isValidMobile = ArrayHelper::getValue($mobile, ['attribute', 'is_valid'], false);
            if ($isValidMobile) {
                $valid = true;
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $userPhone = new UserPhone();
                    $userPhone->userId = $this->user->id;
                    $userPhone->prefix = ArrayHelper::getValue($mobile, ['mobile', 'prefix']);
                    $userPhone->number = ArrayHelper::getValue($mobile, ['mobile', 'number']);
                    $userPhone->complete = ArrayHelper::getValue($mobile, ['mobile', 'complete']);
                    $userPhone->isVerified = true;
                    $valid = $valid && $userPhone->save();
                    $valid = $valid && $this->user->resetDefaultPhone();

                    if ($userPhone->hasErrors()) {
                        $this->errorMessage = 'Failed to link phone...';
                        Yii::error($userPhone->errors);
                    }

                    $valid ? $transaction->commit() : $transaction->rollBack();
                } catch (\Exception $e) {
                    $valid = false;
                    $transaction->rollBack();
                    Yii::error($e);
                }

                return $valid;
            }

            $this->errorMessage = 'Failed to link phone...';
            if (!empty($reason = ArrayHelper::getValue($mobile, ['reason']))) {
                $this->errorMessage = $reason;
            }
            return false;
        } elseif ($userPhone->userId === $this->user->id) {
            return true;
        }

        $this->errorMessage = 'The phone is already linked to another user in our system.';
        return false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function processFacebook()
    {
        $facebookId = ArrayHelper::getValue($this->_identities, [AuthIdentityType::FACEBOOK]);
        if (empty($facebookId)) {
            throw new InvalidCallException('Invalid token, the value given is not a facebook token !');
        }

        $userSocialFacebook = UserSocial::find()->facebook($facebookId)->active()->limit(1)->one();
        if ($userSocialFacebook === null) {
            $userSocialFacebook = new UserSocial();
            $userSocialFacebook->userId = $this->user->id;
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

            if ($userSocialFacebook->save()) {
                return true;
            }

            if ($userSocialFacebook->hasErrors()) {
                $this->errorMessage = 'Failed to link Facebook...';
                Yii::error($userSocialFacebook->errors);
            }

            return false;
        } elseif ($userSocialFacebook->userId === $this->user->id) {
            return true;
        }

        $this->errorMessage = 'The Facebook ID is already linked to another user in our system.';
        return false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function processGoogle()
    {
        $googleId = ArrayHelper::getValue($this->_identities, [AuthIdentityType::GOOGLE]);
        if (empty($googleId)) {
            throw new InvalidCallException('Invalid token, the value given is not a google token !');
        }

        $userSocialGoogle = UserSocial::find()->google($googleId)->active()->limit(1)->one();
        if ($userSocialGoogle === null) {
            $userSocialGoogle = new UserSocial();
            $userSocialGoogle->userId = $this->user->id;
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

            if ($userSocialGoogle->save()) {
                return true;
            }

            if ($userSocialGoogle->hasErrors()) {
                $this->errorMessage = 'Failed to link Google...';
                Yii::error($userSocialGoogle->errors);
            }

            return false;
        } elseif ($userSocialGoogle->userId === $this->user->id) {
            return true;
        }

        $this->errorMessage = 'The Google ID is already linked to another user in our system.';
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

        if ($this->type === AuthIdentityType::PHONE) {

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
}