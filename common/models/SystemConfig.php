<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\ConfigType;
use Yii;

/**
 * This is the model class for table "{{%system_config}}".
 *
 * @property string $id
 * @property string $type
 * @property string $name
 * @property string|null $description
 * @property string|null $content
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 */
class SystemConfig extends ActiveRecord
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
                'ignored' => ['type'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%system_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['description'], 'string'],
            [['content'], 'safe'],
            [['type'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 128],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'type' => Yii::t('model', 'system_config.type'),
            'name' => Yii::t('model', 'system_config.name'),
            'description' => Yii::t('model', 'system_config.description'),
            'content' => Yii::t('model', 'system_config.content'),
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
    public function getIsScalar()
    {
        return true;
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
        } else {
            return $this->content;
        }
    }

    /**
     * {@inheritdoc}
     * @return SystemConfigQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SystemConfigQuery(get_called_class());
    }
}
