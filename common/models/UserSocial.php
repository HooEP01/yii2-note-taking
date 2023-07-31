<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\SocialChannel;
use Yii;

/**
 * This is the model class for table "{{%user_social}}".
 *
 * @property string $id
 * @property string $userId
 * @property string $channel e.g. Facebook, Google, WeChat etc
 * @property string $channelId The actual id of the channel
 * @property string|null $channelName Name from the channel
 * @property string|null $channelAvatarImageSrc Avatar Image Src from the channel
 * @property bool $isVerified
 * @property string $token
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property User $user
 */
class UserSocial extends ActiveRecord
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
        return '{{%user_social}}';
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return [
            'id' => 'shortUuid',
            'channel' => function () {
                return [
                    'code' => $this->channel,
                    'name' => SocialChannel::resolve($this->channel),
                ];
            },
            'value' => 'channelId',
            'name' => 'channelName',
            'avatarImageSrc' => 'channelAvatarImageSrc',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'channel', 'channelId'], 'required'],
            [['userId'], 'default', 'value' => null],
            [['userId'], 'string'],
            [['isVerified'], 'boolean'],
            [['channel'], 'string', 'max' => 32],
            [['channelId', 'channelName'], 'string', 'max' => 256],
            [['token'], 'string', 'max' => 128],
            [['channel', 'channelId'], 'unique', 'targetAttribute' => ['channel', 'channelId']],
            [['userId', 'channel'], 'unique', 'targetAttribute' => ['userId', 'channel']],
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
            'channel' => Yii::t('model', 'user_social.channel'),
            'channelId' => Yii::t('model', 'user_social.channelId'),
            'channelName' => Yii::t('model', 'user_social.channelName'),
            'isVerified' => Yii::t('model', 'common.isVerified'),
            'token' => Yii::t('model', 'common.token'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return bool
     */
    public function softDelete(): bool
    {
        $this->channel = $this->channel . '_deleted_' . substr($this->id, 0, 8);
        $this->channelId = $this->channelId . '_deleted_' . substr($this->id, 0, 8);
        return parent::softDelete();
    }

    /**
     * @return bool
     */
    public function softRestore(): bool
    {
        $this->channel = substr($this->channel, 0, strpos($this->channel, '_deleted_'));
        $this->channelId = substr($this->channelId, 0, strpos($this->channelId, '_deleted_'));
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * {@inheritdoc}
     * @return UserSocialQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserSocialQuery(get_called_class());
    }
}
