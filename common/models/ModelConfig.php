<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\ConfigType;
use common\base\helpers\Json;
use Yii;

/**
 * This is the model class for table "{{%model_config}}".
 *
 * @property string $id
 * @property string|null $ownerType
 * @property string|null $ownerKey
 * @property string $name
 * @property string $type
 * @property string|null $remark
 * @property string|null $content
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property string|null $deletedBy
 * @property string|null $deletedAt
 * @property bool $isActive
 */
class ModelConfig extends ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'blameable' => [
                'class' => 'common\base\behaviors\BlameableBehavior',
            ],
            'audit' => [
                'class' => 'common\base\audit\behaviors\AuditTrailBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%model_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['remark'], 'string'],
            [['content'], 'safe'],
            [['type'], 'string', 'max' => 64],
            [['ownerType', 'ownerKey', 'name'], 'string', 'max' => 128],
            [['ownerType', 'ownerKey', 'name'], 'unique', 'targetAttribute' => ['ownerType', 'ownerKey', 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'ownerType' => Yii::t('model', 'common.ownerType'),
            'ownerKey' => Yii::t('model', 'common.ownerKey'),
            'name' => Yii::t('model', 'common.name'),
            'type' => Yii::t('model', 'model_config.type'),
            'remark' => Yii::t('model', 'common.remark'),
            'content' => Yii::t('model', 'common.content'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'deletedBy' => Yii::t('model', 'common.deletedBy'),
            'deletedAt' => Yii::t('model', 'common.deletedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->type == ConfigType::INTEGER) {
            return (integer) $this->content;
        } elseif ($this->type == ConfigType::DECIMAL) {
            return (float) $this->content;
        } elseif ($this->type == ConfigType::BOOLEAN) {
            return (bool) $this->content;
        } elseif ($this->type === ConfigType::ARRAY) {
            if (Json::validate($this->content)) {
                return Json::decode($this->content);
            }
            return [];
        } else {
            return $this->content;
        }
    }

    /**
     * @param mixed $value
     * @return static
     */
    public static function factory($value)
    {
        $model = new static();
        if ($value instanceof ActiveRecord) {
            $model->setOwnerModel($value);
        }

        return $model;
    }

    /**
     * @param ActiveRecord $model
     */
    public function setOwnerModel(ActiveRecord $model)
    {
        $this->ownerType = $model->tableName();
        $this->ownerKey = implode(',', $model->getPrimaryKey(true));
    }

    /**
     * {@inheritdoc}
     * @return ModelConfigQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ModelConfigQuery(get_called_class());
    }
}
