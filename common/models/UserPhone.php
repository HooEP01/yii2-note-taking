<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\DateTime;
use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\MobilePrefix;
use common\base\helpers\UuidHelper;
use Yii;

/**
 * This is the model class for table "{{%user_phone}}".
 *
 * @property string $id
 * @property string $userId
 * @property string $prefix
 * @property string $number
 * @property string $complete
 * @property bool $isVerified
 * @property string $token
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property string $deletedBy
 * @property string $deletedAt
 * @property bool $isActive
 *
 * @property User $user
 * @property User[] $users
 */
class UserPhone extends ActiveRecord
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
                'stripCleanAttributes' => ['prefix', 'number'],
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
        return '{{%user_phone}}';
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
            'prefix',
            'number',
            'complete',
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
            [['number'], 'filter', 'filter' => function ($value) {
                $value = ltrim($value, '0');
                return $value;
            }],
            [['userId', 'prefix', 'number'], 'required'],
            [['userId'], 'string'],
            [['isVerified'], 'boolean'],
            [['prefix'], 'string', 'max' => 12],
            [['number'], 'string', 'max' => 32],
            [['complete'], 'string', 'max' => 64],
            [['token'], 'string', 'max' => 128],
            [['complete'], 'unique'],
            [['token'], 'unique'],

            [['prefix'], 'in', 'range' => MobilePrefix::values()],
            [['number'], 'trim'],
            [['number'], 'match', 'pattern' => '/^[0-9]+$/'],

            [['prefix', 'number'], 'unique', 'targetAttribute' => ['prefix', 'number']],
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
            'prefix' => Yii::t('model', 'user_phone.prefix'),
            'number' => Yii::t('model', 'user_phone.number'),
            'complete' => Yii::t('model', 'user_phone.complete'),
            'isVerified' => Yii::t('model', 'user_phone.isVerified'),
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
    public function getComplete()
    {
        return $this->complete;
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
            return $user->phoneId === $this->id;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function setAsDefault()
    {
        $this->user->phoneId = $this->id;
        return $this->user->save();
    }

    /**
     * @return bool
     */
    public function softDelete(): bool
    {
        $this->number = $this->number . '_deleted_' . substr($this->id, 0, 8);
        $this->complete = $this->complete . '_deleted_' . substr($this->id, 0, 8);
        return parent::softDelete();
    }

    /**
     * @return bool
     */
    public function softRestore(): bool
    {
        $this->number = substr($this->number, 0, strpos($this->number, '_deleted_'));
        $this->complete = substr($this->complete, 0, strpos($this->complete, '_deleted_'));
        return parent::softRestore();
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        $this->complete = $this->prefix . $this->number;

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
        $this->user->resetDefaultPhone();
        return parent::afterSave($insert, $changedAttributes);
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
     * @return UserPhoneQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserPhoneQuery(get_called_class());
    }
}
