<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use Yii;

/**
 * This is the model class for table "{{%note}}".
 *
 * @property int $id
 * @property string $folder_id
 * @property string $title
 * @property string $description
 * @property string|null $tags from enum
 * @property string|null $priority
 * @property int|null $due_date
 * @property string|null $status
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property string|null $deletedBy
 * @property string|null $deletedAt
 * @property bool $isActive
 */
class Note extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%note}}';
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
            [['folder_id', 'title'], 'required'],
            [['folder_id', 'title', 'description'], 'string'],
            [['due_date'], 'default', 'value' => null],
            [['priority'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'folder_id' => Yii::t('model', 'note.folder_id'),
            'title' => Yii::t('model', 'common.title'),
            'description' => Yii::t('model', 'common.description'),
            'tags' => Yii::t('model', 'common.tags'),
            'priority' => Yii::t('model', 'note.priority'),
            'due_date' => Yii::t('model', 'note.due_date'),
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


    public function getFolder()
    {
        return $this->hasOne(Folder::class, ['id' => 'folder_id']);
    }
    

    /**
     * {@inheritdoc}
     * @return NoteQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NoteQuery(get_called_class());
    }
}
