<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\FolderStatus;
use Yii;

/**
 * This is the model class for table "{{%folder}}".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $status
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property string|null $deletedBy
 * @property string|null $deletedAt
 * @property bool $isActive
 */
class Folder extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%folder}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BehaviorCode::BLAMEABLE => [
                'class' => 'common\base\behaviors\BlameableBehavior',
            ],
            BehaviorCode::NULL => [
                'class' => 'common\base\behaviors\NullOnEmptyBehavior',
                'attributes' => [
                    'title', 'description', 'status',
                ],
            ],
            BehaviorCode::SANITIZE => [
                'class' => 'common\base\behaviors\SanitizeBehavior',
                'stripCleanAttributes' => [
                    'title', 'description',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'in', 'range' => FolderStatus::values()],
            [['title', 'description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'title' => Yii::t('model', 'common.title'),
            'description' => Yii::t('model', 'common.description'),
            'status' => Yii::t('model', 'common.status'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'deletedBy' => Yii::t('model', 'common.deletedBy'),
            'deletedAt' => Yii::t('model', 'common.deletedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    public function getNotes()
    {
        return $this->hasMany(Note::class, ['folder_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return FolderQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FolderQuery(get_called_class());
    }
}
