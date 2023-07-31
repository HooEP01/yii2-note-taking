<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use Yii;

/**
 * This is the model class for table "{{%model_translation}}".
 *
 * @property int $id
 * @property string $language
 * @property string $code
 * @property string|null $ownerType Table name of the model or class of owner
 * @property string|null $ownerKey The string representation of pk or id
 * @property string|null $ownerAttribute The attribute name
 * @property string $message
 * @property string|null $content
 * @property int $createdBy
 * @property string $createdAt
 * @property int $updatedBy
 * @property string $updatedAt
 * @property int $isActive
 *
 * @method processSyncData($data, $options = [])
 */
class ModelTranslation extends ActiveRecord
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%model_translation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'message'], 'required'],
            [['content'], 'string'],
            [['language', 'message'], 'string', 'max' => 255],
            [['code', 'ownerType', 'ownerKey', 'ownerAttribute'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'language' => Yii::t('model', 'model_translation.language'),
            'code' => Yii::t('model', 'common.code'),
            'ownerType' => Yii::t('model', 'common.ownerType'),
            'ownerKey' => Yii::t('model', 'common.ownerKey'),
            'ownerAttribute' => Yii::t('model', 'model_translation.ownerAttribute'),
            'message' => Yii::t('model', 'model_translation.message'),
            'content' => Yii::t('model', 'model_translation.content'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ModelTranslationQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ModelTranslationQuery(get_called_class());
    }
}
