<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\models;

use common\base\DateTime;
use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\ConfigName;
use common\base\enum\CurrencyCode;
use common\base\enum\Gender;
use common\base\enum\LanguageCode;
use common\base\enum\MobilePrefix;
use common\base\enum\NameFormat;
use common\base\enum\QrCodeType;
use common\base\enum\UserRole;
use common\base\enum\UserStatus;
use common\base\enum\UserType;
use common\base\helpers\ArrayHelper;
use common\base\helpers\Json;
use common\base\helpers\NanoIdHelper;
use common\base\helpers\StringHelper;
use common\base\helpers\Url;
use common\base\helpers\UuidHelper;
use common\jobs\SendEmail;
use common\jobs\UserPasswordReset;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;
use yii\db\ArrayExpression;
use yii\db\Exception;
use yii\web\IdentityInterface;
use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property string $id
 * @property ArrayExpression $roles
 * @property string $type
 * @property string $status
 * @property string $username
 * @property string|null $name
 * @property string|null $displayName
 * @property int|null $emailId Default Email
 * @property int|null $phoneId Default Phone
 * @property int|null $addressId Default Address
 * @property int|null $imageId User Avatar Image
 * @property string|null $firstName
 * @property string|null $middleName
 * @property string|null $lastName
 * @property string|null $fullName
 * @property string|null $nameFormat
 * @property string|null $description
 * @property string $gender
 * @property string|null $dateOfBirth
 * @property string $passwordSalt
 * @property string $passwordHash
 * @property string|null $passwordResetToken
 * @property string $token
 * @property string $authKey
 * @property string $authMfaToken
 * @property string $authCookieExpiry
 * @property string $referrerCode
 * @property string|null $referrerUserId
 * @property string $languageCode
 * @property string $currencyCode
 * @property string|null $countryCode
 * @property string|null $stateCode
 * @property string|null $cityId
 * @property string|null $postcode
 * @property string|null $lastActivityPk
 * @property array $lastActivityData
 * @property array $configuration
 * @property string|null $cacheDeviceIdentifier
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property Image $image
 * @property Image|null $imageOrDefault
 * @property UserEmail $email
 * @property UserPhone $phone
 * @property Address $address
 * @property UserEmail[] $emails
 * @property UserPhone[] $phones
 * @property UserSocial[] $socials
 * @property Address[] $addresses
 * @property Currency $currency
 *
 * @method ImageQuery getImage()
 * @method ImageQuery getImages()
 * @method Image getImageModel()
 * @method void setImage(Image $value)
 * @method boolean resetImage()
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $roleItems = [];

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BehaviorCode::BLAMEABLE => [
                'class' => 'common\base\behaviors\BlameableBehavior',
            ],
            BehaviorCode::SANITIZE => [
                'class' => 'common\base\behaviors\SanitizeBehavior',
                'stripCleanAttributes' => ['firstName', 'lastName', 'middleName', 'fullName', 'description', 'displayName']
            ],
            BehaviorCode::AUDIT => [
                'class' => 'common\base\audit\behaviors\AuditTrailBehavior',
                'ignored' => [
                    'passwordSalt', 'passwordHash', 'nameFormat', 'token',
                ]
            ],
            BehaviorCode::IMAGE => [
                'class' => 'common\base\behaviors\ImageBehavior',
                'fallbackImageSrc' => 'default/user-avatar.jpg',
                'defaultImageCode' => 'default-user-avatar',
            ],
            BehaviorCode::ARRAY_EXPRESSION => [
                'class' => 'common\base\behaviors\ArrayExpressionBehavior',
                'attributes' => [
                    'roles' => 'roleItems'
                ],
            ],
            BehaviorCode::TOKEN => [
                'class' => 'common\base\behaviors\TokenBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return [
            'id' => function () {
                return UuidHelper::encodeShort($this->id);
            },
            'avatar' => function () {
                $data = (new Image())->toArray();
                if ($this->image) {
                    $data = $this->image->toArray();
                    if ($this->image->getHasImage()) {
                        return $data;
                    }
                }

                $default = $this->getAvatarImageSrc();

                ArrayHelper::setValue($data, 'large.src', $default);
                ArrayHelper::setValue($data, 'small.src', $default);
                ArrayHelper::setValue($data, 'medium.src', $default);

                return $data;
            },
            'username',
            'displayName',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['roleItems'], 'each', 'rule' => ['in', 'range' => UserRole::values()]],
            [['username', 'passwordSalt', 'passwordHash', 'authKey'], 'required'],
            [['emailId', 'phoneId', 'imageId', 'referrerUserId'], 'string'],
            [['dateOfBirth'], 'datetime', 'format' => 'php:Y-m-d'],
            [['status', 'authKey'], 'string', 'max' => 64],
            [['username', 'passwordSalt', 'passwordResetToken', 'token', 'cacheDeviceIdentifier'], 'string', 'max' => 128],
            [['name', 'displayName', 'firstName', 'middleName', 'lastName', 'fullName', 'nameFormat'], 'string', 'max' => 254],
            [['gender', 'referrerCode', 'languageCode'], 'string', 'max' => 32],
            [['passwordHash'], 'string', 'max' => 256],
            [['currencyCode', 'countryCode'], 'string', 'max' => 8],
            [['stateCode'], 'string', 'max' => 25],
            [['postcode'], 'string', 'max' => 16],
            [['passwordResetToken'], 'unique'],
            [['referrerCode'], 'unique'],
            [['username'], 'unique'],
            [['imageId'], 'exist', 'skipOnError' => true, 'targetClass' => Image::class, 'targetAttribute' => ['imageId' => 'id']],
            [['emailId'], 'exist', 'skipOnError' => true, 'targetClass' => UserEmail::class, 'targetAttribute' => ['emailId' => 'id']],
            [['phoneId'], 'exist', 'skipOnError' => true, 'targetClass' => UserPhone::class, 'targetAttribute' => ['phoneId' => 'id']],
            [['addressId'], 'exist', 'skipOnError' => true, 'targetClass' => Address::class, 'targetAttribute' => ['addressId' => 'id']],
            [['countryCode'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['countryCode' => 'code'], 'on' => 'profile-update'],
            [['gender'], 'default', 'value' => Gender::OTHER],
            [['gender'], 'in', 'range' => Gender::values()],
            //            [['languageCode'], 'in', 'range' => LanguageCode::values()],
            [['currencyCode'], 'in', 'range' => CurrencyCode::supported()],
            [['nameFormat'], 'in', 'range' => NameFormat::values()],
            [['roles', 'description', 'emailId', 'phoneId', 'imageId', 'referrerUserId'], 'default', 'value' => null],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['profile-update'] = [
            'firstName', 'lastName', 'gender', 'countryCode'
        ];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'roles' => Yii::t('model', 'user.roles'),
            'roleItems' => Yii::t('model', 'user.roles'),
            'status' => Yii::t('model', 'common.status'),
            'username' => Yii::t('model', 'user.username'),
            'name' => Yii::t('model', 'common.name'),
            'displayName' => Yii::t('model', 'common.displayName'),
            'emailId' => Yii::t('model', 'user.emailId'),
            'phoneId' => Yii::t('model', 'user.phoneId'),
            'imageId' => Yii::t('model', 'user.imageId'),
            'firstName' => Yii::t('model', 'user.firstName'),
            'middleName' => Yii::t('model', 'user.middleName'),
            'lastName' => Yii::t('model', 'user.lastName'),
            'fullName' => Yii::t('model', 'user.fullName'),
            'nameFormat' => Yii::t('model', 'user.nameFormat'),
            'description' => Yii::t('model', 'user.description'),
            'gender' => Yii::t('model', 'user.gender'),
            'dateOfBirth' => Yii::t('model', 'user.dateOfBirth'),
            'passwordSalt' => Yii::t('model', 'user.passwordSalt'),
            'passwordHash' => Yii::t('model', 'user.passwordHash'),
            'passwordResetToken' => Yii::t('model', 'user.passwordResetToken'),
            'token' => Yii::t('model', 'common.token'),
            'authKey' => Yii::t('model', 'user.authKey'),
            'referrerCode' => Yii::t('model', 'user.referrerCode'),
            'referrerUserId' => Yii::t('model', 'user.referrerUserId'),
            'languageCode' => Yii::t('model', 'common.languageCode'),
            'currencyCode' => Yii::t('model', 'common.currencyCode'),
            'countryCode' => Yii::t('model', 'common.countryCode'),
            'stateCode' => Yii::t('model', 'common.stateCode'),
            'postcode' => Yii::t('model', 'user.postcode'),
            'cacheDeviceIdentifier' => Yii::t('model', 'user.cacheDeviceIdentifier'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return Wallet
     */
    public function getDefaultWallet()
    {
        return $this->getOrCreateWallet($this->currencyCode);
    }

    /**
     * @return Wallet
     */
    public function getPointWallet()
    {
        return $this->getOrCreateWallet(CurrencyCode::HUSTLE_POINT);
    }

    /**
     * @param null|string|Currency $currency
     * @return Wallet
     */
    public function getOrCreateWallet($currency = null)
    {
        $key = md5(__METHOD__ . @serialize($currency));
        return $this->getOrSetRuntimeData($key, function () use ($currency) {
            if (($wallet = $this->getWallet($currency)) instanceof Wallet) {
                return $wallet;
            }

            $this->createWallet($currency);
            if (($wallet = $this->getWallet($currency)) instanceof Wallet) {
                return $wallet;
            } else {
                throw new Exception('Failed to create wallet !');
            }
        });
    }

    /**
     * @return Wallet|null
     */
    public function getPoint()
    {
        return $this->getWallet(CurrencyCode::HUSTLE_POINT);
    }

    /**
     * @param null|string $currency
     * @return Wallet|null
     */
    public function getWallet($currency = null)
    {
        $query = Wallet::find()->owner($this)->currency($this->getCurrencyCode($currency))->active()->limit(1);
        return $query->one();
    }

    /**
     * @return Wallet[]|array
     */
    public function getWallets()
    {
        $query = Wallet::find()->owner($this)->active();
        return $query->all();
    }

    /**
     * @param string|Currency $currency
     * @return bool|Wallet
     */
    public function createWallet($currency = null)
    {
        $currencyCode = $this->getCurrencyCode($currency);
        $query = Wallet::find()->owner($this)->currency($currencyCode)->active()->limit(1);
        if ($query->exists()) {
            return true;
        }

        $wallet = Wallet::factory($this);
        $wallet->currencyCode = $currencyCode;
        $wallet->precision = $wallet->currency->precision;
        $valid = $wallet->save();

        if ($wallet->hasErrors()) {
            Yii::error($wallet->errors);
        }
        return $valid;
    }

    /**
     * @param null|string $currency
     * @return mixed|string|null
     */
    protected function getCurrencyCode($currency = null)
    {
        $currencyCode = $currency;
        if ($currency === null) {
            $currencyCode = $this->currencyCode;
        } elseif ($currency instanceof Currency) {
            $currencyCode = $currency->code;
        }

        return $currencyCode;
    }

    /**
     * @return bool
     */
    public function getIsPasswordReset()
    {
        return !empty($this->passwordResetToken);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function getIsSystemAdmin()
    {
        $ids = ArrayHelper::getValue(Yii::$app->params, 'system.admin.ids', []);
        return in_array($this->id, $ids);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function getIsSuperAdmin()
    {
        if ($this->getIsSystemAdmin()) {
            return true;
        }

        $roles = $this->getUserRoles();
        return in_array(UserRole::SUPER_ADMIN, $roles);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function getIsAdmin()
    {
        if ($this->getIsSuperAdmin()) {
            return true;
        }

        $roles = $this->getUserRoles();
        return in_array(UserRole::ADMIN, $roles);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getAvatarImageSrc()
    {
        if (($image = $this->imageOrDefault) instanceof Image && $image->getHasImage()) {
            return $image->getImageSrc();
        }

        return Url::base(true) . '/uploads/default/user-avatar.jpg';
    }

    /**
     * @return bool
     */
    public function getHasAvatarImage()
    {
        return $this->image && $this->image->getHasImage();
    }

    /**
     * @return array
     */
    public function getUserRoles()
    {
        if (!empty($this->roleItems)) {
            return $this->roleItems;
        }

        if ($this->roles instanceof ArrayExpression) {
            return $this->roleItems = $this->roles->getValue();
        }

        return [];
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getDateJoined()
    {
        $datetime = new DateTime($this->createdAt);
        return $datetime->format('M Y');
    }

    /**
     * @return string
     */
    public function getDisplayNameWithPhone()
    {
        return sprintf('%s [%s]', $this->getDisplayName(), $this->phone ? $this->phone->getComplete() : '-');
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (empty($this->displayName)) {
            if (empty($this->name)) {
                return $this->getMaskedUsername();
            }

            return $this->name;
        }

        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getMaskedUsername()
    {
        if (($pos = strpos($this->username, '@')) !== false) {
            return substr($this->username, 0, $pos);
        }

        if ($this->email && ($pos = strpos($this->email->address, '@')) !== false) {
            return substr($this->email->address, 0, $pos);
        }

        if ($this->phone && !empty($this->phone->complete)) {
            $phone = $this->phone->complete;
            return substr($phone, 0, strlen($phone) - 5) . 'XXXXX';
        }

        return $this->username;
    }

    /**
     * @return string
     */
    public function getReferrerCode()
    {
        return $this->referrerCode;
    }

    /**
     * get the payload data required by web token
     * @return array
     */
    public function getTokenPayload()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->getDisplayName(),
            'key' => $this->getAuthKey(),
            'exp' => time() + 3600 * 24 * 90,
        ];
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        if (empty($this->languageCode)) {
            return LanguageCode::ENGLISH;
        }

        return $this->languageCode;
    }

    /**
     * @return void
     */
    public function sendTemporaryPasswordEmail()
    {
        if (!empty($this->temporaryPassword) && $this->email) {
            $job = new SendEmail([
                'to' => [$this->email->address => $this->name],
                'template' => 'temporaryPassword.twig',
                'subject' => 'Temporary Password',
                'params' => ['password' => $this->temporaryPassword],
            ]);
            Yii::$app->queue->push($job);
        }
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->generateAuthKey();
        $this->waivePasswordResetToken();
        $this->passwordSalt = Yii::$app->security->generateRandomString();
        $this->passwordHash = self::hashPassword($password, $this->passwordSalt);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $hash = static::hashPassword($password, $this->passwordSalt);
        return $hash === $this->passwordHash;
    }

    /**
     * Get User's Password Reset Token
     * @return string
     */
    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * Generates new password reset token
     * @throws \yii\base\Exception
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Finds out if password reset token is valid
     * @return bool
     */
    public function getIsPasswordResetTokenValid()
    {
        $token = $this->getPasswordResetToken();

        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * waive password reset token
     * @return void
     */
    public function waivePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }

    /**
     * validate password reset token
     * @param $token
     * @return bool
     */
    public function validatePasswordResetToken($token)
    {
        return $token === $this->getPasswordResetToken();
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->authKey = NanoIdHelper::generate(32);
    }

    /**
     * @return string
     */
    public function getOtpToken()
    {
        return Base32::encode($this->authMfaToken);
    }

    /**
     * @return \OTPHP\TOTPInterface
     */
    public function getTimeBaseOTP()
    {
        $otp = TOTP::create($this->getOtpToken());
        $otp->setLabel('Hustle Hero');
        return $otp;
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function resetAuthMfaToken()
    {
        $this->authMfaToken = Yii::$app->security->generateRandomString(128);
        return $this->save(false, ['authMfaToken']);
    }

    /**
     * @return bool
     */
    public function getIsMfaRequired()
    {
        $enforce = (bool) Yii::$app->config->get(ConfigName::ENFORCE_SUPER_ADMIN_MFA, false);
        if ($this->getIsSuperAdmin() && $enforce) {
            return true;
        }

        return !empty($this->authMfaToken);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getQrCodeValue()
    {
        $data = [
            'type' => QrCodeType::USER,
            'id' => $this->id,
            'token' => $this->token,
        ];

        $dataJson = Json::encode($data);
        $password = ArrayHelper::getValue(Yii::$app->params, 'security.encrypt.password', '-');
        $dataEncrypted = Yii::$app->security->encryptByPassword($dataJson, $password);

        $data = [
            'v' => 1,
            'type' => QrCodeType::USER,
            'value' => $this->getDisplayName(),
            'data' => base64_encode($dataEncrypted),
        ];

        $json = Json::encode($data);
        Yii::endProfile(__METHOD__);

        return $json;
    }

    /**
     * @param string $channel
     * @return bool
     */
    public function getIsSocialAccountConnected($channel)
    {
        $query = UserSocial::find()->user($this)->channel($channel)->verified()->active();

        return $query->exists();
    }

    /**
     * @return bool
     */
    public function resetDefaultEmail()
    {
        $query = $this->getEmails()->active();
        $count = (int) $query->count();
        if ($count === 0) {
            $this->emailId = null;
            return $this->save();
        } elseif ($count === 1 && ($model = $query->one())) {
            $this->emailId = $model->id;
            return $this->save();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function resetDefaultPhone()
    {
        $query = $this->getPhones()->active();
        $count = (int) $query->count();
        if ($count === 0) {
            $this->phoneId = null;
            return $this->save();
        } elseif ($count === 1 && ($model = $query->one())) {
            $this->phoneId = $model->id;
            return $this->save();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function resetDefaultAddress()
    {
        $query = $this->getAddresses()->active();
        $count = (int) $query->count();
        if ($count === 0) {
            $this->addressId = null;
            return $this->save();
        } elseif ($count === 1 && ($model = $query->one())) {
            $this->addressId = $model->id;
            return $this->save();
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getReferrer()
    {
        return $this->hasOne(User::class, ['id' => 'referrerUserId']);
    }

    /**
     * @return \yii\db\ActiveQuery|UserEmailQuery
     */
    public function getEmail()
    {
        return $this->hasOne(UserEmail::class, ['id' => 'emailId']);
    }

    /**
     * @return \yii\db\ActiveQuery|UserPhoneQuery
     */
    public function getPhone()
    {
        return $this->hasOne(UserPhone::class, ['id' => 'phoneId']);
    }

    /**
     * @return \yii\db\ActiveQuery|AddressQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::class, ['id' => 'addressId']);
    }

    /**
     * @return \yii\db\ActiveQuery|UserEmailQuery
     */
    public function getEmails()
    {
        $query = $this->hasMany(UserEmail::class, ['userId' => 'id']);
        return $query;
    }

    /**
     * @return \yii\db\ActiveQuery|UserPhoneQuery
     */
    public function getPhones()
    {
        $query = $this->hasMany(UserPhone::class, ['userId' => 'id']);
        return $query;
    }

    /**
     * @return \yii\db\ActiveQuery|AddressQuery
     */
    public function getAddresses()
    {
        $query = $this->hasMany(Address::class, ['ownerKey' => 'id'])->where(['ownerType' => self::tableName()]);
        return $query;
    }

    /**
     * @return \yii\db\ActiveQuery|CurrencyQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['code' => 'currencyCode']);
    }

    /**
     * @return \yii\db\ActiveQuery|CountryQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['code' => 'countryCode']);
    }

    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if (empty($this->nameFormat)) {
            $this->nameFormat = NameFormat::FIRST_LAST;
        }

        if (empty($this->username)) {
            if (!empty($email = $this->getEmail()->one())) {
                $this->username = $email;
            } else {
                $this->username = StringHelper::randomString(32);
            }
        }

        if (empty($this->referrerCode)) {
            $this->referrerCode = StringHelper::randomString(8, true, false, 'upper');
        }

        if (empty($this->currencyCode)) {
            $this->currencyCode = CurrencyCode::AUSTRALIAN_DOLLAR;
        }

        if (!empty($this->nameFormat)) {
            $this->name = strtr($this->nameFormat, [
                '{lastName}' => $this->lastName,
                '{firstName}' => $this->firstName,
            ]);

            $this->name = trim($this->name);

            if (empty($this->displayName)) {
                $this->displayName = $this->name;
            }
        }

        if ($this->getIsNewRecord() && empty($this->passwordResetToken)) {
            $this->generatePasswordResetToken();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function hashPassword($password, $salt = '')
    {
        return hash('sha256', $password . '-Say-Cheese-' . $salt);
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return User|IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given username or email.
     * @param string $value
     * @return User|IdentityInterface the identity object that matches the given ID.
     */
    public static function findIdentityByUsername($value)
    {
        return static::find()->username($value)->one();
    }

    /**
     * Finds an identity by the given email.
     * @param string $email
     * @return User|IdentityInterface the identity object that matches the given ID.
     */
    public static function findIdentityByEmail($email)
    {
        $model = UserEmail::find()->email($email)->active()->one();
        if ($model !== null) {
            return $model->user;
        }

        return null;
    }

    /**
     * Finds an identity by the given phone number.
     * @param $phone
     * @return User|mixed
     */
    public static function findIdentityByPhone($phone)
    {
        $complete = MobilePrefix::MALAYSIA . ltrim($phone, '0');
        if (substr($phone, 0, 1) == '+') {
            $complete = $phone;
        } elseif (substr($phone, 0, 1) === '6') {
            $complete = '+' . $phone;
        }

        $model = UserPhone::find()->complete($complete)->active()->one();
        if ($model !== null) {
            return $model->user;
        }

        return null;
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type  the type of the token. The value of this parameter depends on the implementation.
     *                     For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be
     *                     `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     *                     Null should be returned if such an identity cannot be found
     *                     or the identity is not in an active state (disabled, deleted, etc.)
     * @throws \yii\base\InvalidConfigException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $data = Yii::$app->jwt->resolveToken($token);

        if (empty($id = ArrayHelper::getValue($data, ['sub']))) {
            return null;
        }
        if (empty($key = ArrayHelper::getValue($data, ['jti']))) {
            return null;
        }

        return static::find()->id($id)->authKey($key)->limit(1)->one();
    }

    /**
     * @param string $token
     * @return array|User|null
     */
    public static function findByPasswordResetToken($token)
    {
        return static::find()->passwordResetToken($token)->one();
    }

    /**
     * Finds user by referrer code
     *
     * @param string $code
     * @return User|IdentityInterface the identity object that matches the given referrer code.
     */
    public static function findIdentityByReferrerCode($code)
    {
        return static::find()->referrerCode($code)->one();
    }

    /**
     * {@inheritdoc}
     * @return UserQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @var $id
     * @return array
     */
    public static function options()
    {
        return Yii::$app->cache->getOrSet(__METHOD__, function () {
            $options = [];
            $query = self::find()->active()->orderBy(['createdAt' => SORT_ASC]);

            /** @var User $user */
            foreach ($query->each(1000) as $user) {
                $options[$user->id] = $user->getDisplayNameWithPhone();
            }

            return $options;
        });
    }

    /**
     * @return static
     * @throws \yii\base\Exception
     */
    public static function factory()
    {
        $user = new static();
        $user->type = UserType::CUSTOMER;
        $user->status = UserStatus::ACTIVE;
        $user->username = sprintf('%s-%s', 'APP', StringHelper::randomString(10, true, false, 'upper'));
        $user->authKey = Yii::$app->security->generateRandomString();
        $user->referrerCode = StringHelper::randomString(8, true, false, 'upper');
        $user->gender = Gender::OTHER;
        $user->currencyCode = CurrencyCode::AUSTRALIAN_DOLLAR;

        $user->setPassword(StringHelper::randomString(12));
        $user->generatePasswordResetToken();

        return $user;
    }
}
