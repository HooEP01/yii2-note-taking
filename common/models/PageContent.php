<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use Yii;

/**
 * This is the model class for table "{{%page_content}}".
 *
 * @property string $id
 * @property string $code
 * @property string|null $slug
 * @property string|null $name
 * @property string|null $title
 * @property string|null $htmlContent
 * @property string|null $purifiedContent
 * @property int $position
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 */
class PageContent extends ActiveRecord
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
                'stripCleanAttributes' => ['code', 'name', 'title'],
                'purifyAttributes' => ['purifiedContent']
            ],
            BehaviorCode::SLUGGABLE => [
                'class' => 'common\base\behaviors\SluggableBehavior',
                'attribute' => 'title',
            ],
            BehaviorCode::TRANSLATION => [
                'class' => 'common\base\behaviors\TranslationBehavior',
                'attributes' => ['title', 'htmlContent', 'purifiedContent'],
            ],
            BehaviorCode::AUDIT => [
                'class' => 'common\base\audit\behaviors\AuditTrailBehavior',
                'ignored' => ['slug', 'purifiedContent']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%page_content}}';
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'code',
            'slug',
            'name',
            'title',
            'content' => 'purifiedContent',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['slug', 'title', 'htmlContent', 'purifiedContent'], 'string'],
            [['code', 'name'], 'string', 'max' => 128],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'code' => Yii::t('model', 'page_content.code'),
            'slug' => Yii::t('model', 'page_content.slug'),
            'name' => Yii::t('model', 'page_content.name'),
            'title' => Yii::t('model', 'page_content.title'),
            'htmlContent' => Yii::t('model', 'page_content.htmlContent'),
            'purifiedContent' => Yii::t('model', 'page_content.purifiedContent'),
            'position' => Yii::t('model', 'common.position'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return PageContentQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PageContentQuery(get_called_class());
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        $this->purifiedContent = $this->htmlContent;
        return parent::beforeValidate();
    }
}
