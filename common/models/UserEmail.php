<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\DateTime;
use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\helpers\UuidHelper;
use Yii;

/**
 * This is the model class for table "{{%user_email}}".
 *
 * @property string $id
 * @property string $userId
 * @property string $address
 * @property bool $isVerified
 * @property string $token
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property User $user
 * @property User[] $users
 */
class UserEmail extends ActiveRecord
{
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
                'stripCleanAttributes' => ['address']
            ],
            BehaviorCode::AUDIT => [
                'class' => 'common\base\audit\behaviors\AuditTrailBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_email}}';
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
            'address',
            'attribute' => function () {
                return $this->getAttributeField();
            }
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'address'], 'required'],
            [['userId'], 'string'],
            [['isVerified'], 'boolean'],
            [['address'], 'string', 'max' => 254],
            [['token'], 'string', 'max' => 128],
            [['address'], 'email'],
            [['address'], 'unique'],
            [['token'], 'unique'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'userId' => Yii::t('model', 'common.userId'),
            'address' => Yii::t('model', 'user_email.address'),
            'isVerified' => Yii::t('model', 'user_email.isVerified'),
            'token' => Yii::t('model', 'common.token'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return array
     */
    protected function getAttributeField()
    {
        return [
            'isVerified' => $this->getIsVerified()
        ];
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return bool
     */
    public function getIsVerified()
    {
        return (bool) $this->isVerified;
    }

    /**
     * @return bool
     */
    public function getIsDefault()
    {
        if (($user = $this->user) !== null) {
            return $user->emailId === $this->id;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function setAsDefault()
    {
        $this->user->emailId = $this->id;
        return $this->user->save();
    }

    /**
     * @return bool
     */
    public function softDelete(): bool
    {
        $this->address = $this->address . '_deleted_' . substr($this->id, 0, 8);
        return parent::softDelete();
    }

    /**
     * @return bool
     */
    public function softRestore(): bool
    {
        $this->address = substr($this->address, 0, strpos($this->address, '_deleted_'));
        return parent::softRestore();
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if (empty($this->token)) {
            $this->token = Yii::$app->security->generateRandomString(128);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->user->resetDefaultEmail();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }


    /**
     * {@inheritdoc}
     * @return UserEmailQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserEmailQuery(get_called_class());
    }
}
