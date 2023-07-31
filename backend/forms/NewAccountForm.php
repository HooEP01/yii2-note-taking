<?php

/**
 * @author RYU Chua <me@ryu.my>
 */

namespace backend\forms;

use common\base\enum\AccountStatus;
use common\base\enum\AccountType;
use common\base\enum\AccountUserRole;
use common\base\enum\AccountUserStatus;
use common\base\enum\ContactType;
use common\base\enum\MobilePrefix;
use common\base\enum\UserStatus;
use common\base\enum\UserType;
use common\models\Account;
use common\models\AccountUser;
use common\models\Contact;
use common\models\Publisher;
use common\models\Subscription;
use common\models\User;
use common\models\UserEmail;
use common\models\UserPhone;
use yii\base\InvalidCallException;
use yii\base\Model;
use Yii;

/**
 * Class NewAccountForm
 * @property Account $account
 * @property Publisher $publisher
 * @package backend\forms
 */
class NewAccountForm extends Model
{
    public $type;
    public $name;
    public $email;
    public $mobilePrefix;
    public $mobileNumber;
    public $publisherId;
    public $licenseNumber;

    /** @var Account */
    private $_account;

    public function init()
    {
        parent::init();

        if (!isset($this->type) || !AccountType::isValid($this->type)) {
            throw new InvalidCallException('Invalid Account Type');
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['mobilePrefix', 'mobileNumber', 'name', 'email'], 'trim'],
            [['type', 'name', 'email', 'mobilePrefix', 'mobileNumber', 'publisherId'], 'required'],
            [['type'], 'in', 'range' => AccountType::values()],
            [['email'], 'email'],
            [['mobilePrefix'], 'in', 'range' => MobilePrefix::values()],
            [['mobileNumber'], 'match', 'pattern' => '/^[0-9]+$/'],
            [['mobileNumber'], 'common\base\validators\MobileNumberValidator'],
            [['publisherId'], 'exist', 'skipOnError' => true, 'targetClass' => Publisher::class, 'targetAttribute' => ['publisherId' => 'id']],
            [['licenseNumber'], 'string', 'min' => 3, 'max' => 24],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $labels = [
            'email' => 'Email Address',
            'mobilePrefix' => 'Mobile Prefix',
            'mobileNumber' => 'Mobile Number',
            'publisherId' =>  'Agency',
            'licenseNumber' => 'REN Number',
        ];

        if ($this->type === AccountType::DEVELOPER) {
            $labels['publisherId'] = 'Development Company';
            $labels['licenseNumber'] = 'Developer Registration Number';
        }

        return $labels;
    }

    /**
     * @return false
     */
    public function process()
    {
        if (!$this->validate()) {
            return false;
        }

        $valid = true;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (($user = $this->tryCreateUser()) instanceof User) {
                $this->account->name = $this->name;
                $this->account->licenseName = '';
                $this->account->licenseNumber = $this->licenseNumber;
                $this->account->ownerUserId = $user->id;
                $this->account->isVerified = true;
                $this->account->publisherId = $this->publisherId;

                $valid = $valid && $this->account->save();
                if ($this->account->hasErrors()) {
                    Yii::error($this->account->errors);
                }

                if ($valid) {
                    $accountUser = new AccountUser();
                    $accountUser->userId = $user->id;
                    $accountUser->accountId = $this->account->id;
                    $accountUser->name = $this->name;
                    $accountUser->status = AccountUserStatus::ACTIVE;
                    $accountUser->roleItems = [AccountUserRole::OWNER];
                    $valid = $valid && $accountUser->save();
                    if ($accountUser->hasErrors()) {
                        Yii::error($accountUser->errors);
                    }
                }

                if ($valid) {
                    if (!empty($this->mobilePrefix) && !empty($this->mobileNumber)) {
                        $contact = Contact::factory($this->account);
                        $contact->type = ContactType::MOBILE_PHONE;
                        $contact->content = $this->mobilePrefix . $this->mobileNumber;

                        $valid = $valid && $contact->save();
                        if ($contact->hasErrors()) {
                            Yii::error($contact->errors);
                        }
                    }
                }

                if ($valid) {
                    //FIXME: set a setting in system ?
                    if ($this->account->subscription === null) {
                        //auto find free subscription if any
                        $subscription = Subscription::find()
                            ->type($this->account->type)
                            ->published()->free()->active()
                            ->orderByLatest()
                            ->limit(1)
                            ->one();
                        if ($subscription instanceof Subscription) {
                            $valid = $valid && $this->account->updateSubscription($subscription);
                        }
                    }
                }
            } else {
                $valid = false;
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
     * @return User|false
     */
    protected function tryCreateUser()
    {
        // try to check if email exist and the user have account already
        $userEmail = UserEmail::find()->address($this->email)->limit(1)->one();
        if ($userEmail instanceof UserEmail) {
            return $this->isAccountExistByUser($userEmail->user) ? false : $userEmail->user;
        }

        // try to check if phone exist and the user have account already
        if (!empty($this->mobilePrefix) && !empty($this->mobileNumber)) {
            $userPhone = UserPhone::find()->prefix($this->mobilePrefix)->number($this->mobileNumber)->limit(1)->one();
            if ($userPhone instanceof UserPhone) {
                return $this->isAccountExistByUser($userPhone->user) ? false : $userPhone->user;
            }
        }

        $valid = true;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = $this->generateNewUser();
            $valid = $valid && $user->save();
            if ($user->hasErrors()) {
                Yii::error($user->errors);
            }

            $userEmail = new UserEmail(['address' => $this->email, 'userId' => $user->id]);
            $valid = $valid && $userEmail->save(false);
            $valid = $valid && $user->resetDefaultEmail();
            if ($userEmail->hasErrors()) {
                Yii::error($userEmail->errors);
            }

            if (!empty($this->mobilePrefix) && !empty($this->mobileNumber)) {
                $userPhone = new UserPhone(['prefix' => $this->mobilePrefix, 'number' => $this->mobileNumber, 'userId' => $user->id]);
                $valid = $valid && $userPhone->save(false);
                $valid = $valid && $user->resetDefaultPhone();
                if ($userPhone->hasErrors()) {
                    Yii::error($userPhone->errors);
                }
            }

            $valid ? $transaction->commit() : $transaction->rollBack();
            return $valid ? $user : false;
        } catch (\Exception $e) {
            $valid = false;
            $transaction->rollBack();
            Yii::error($e);
        }

        return $valid;
    }

    /**
     * @return User
     * @throws \yii\base\Exception
     */
    protected function generateNewUser()
    {
        $user = User::factory();
        $user->status = UserStatus::ACTIVE;
        $user->type = UserType::ACCOUNT;

        return $user;
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isAccountExistByUser($user)
    {
        return Account::find()->alias('a')
            ->type($this->type)
            ->ownerUser($user)
            ->exists();
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        if (isset($this->_account)) {
            return $this->_account;
        }

        return $this->_account = new Account([
            'type' => $this->type,
            'status' => AccountStatus::ACTIVE,
        ]);
    }

    /**
     * @return Publisher|null
     */
    public function getPublisher()
    {
        if (!empty($this->publisherId)) {
            return Publisher::findOne($this->publisherId);
        }

        return null;
    }
}
