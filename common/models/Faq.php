<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%faq}}".
 *
 * @property string $id
 * @property string $type
 * @property string|null $question
 * @property string|null $htmlAnswer
 * @property string|null $purifiedAnswer
 * @property string|null $categoryIds
 * @property int|null $position
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 */
class Faq extends ActiveRecord
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
                'stripCleanAttributes' => ['type', 'question'],
                'purifyAttributes' => ['purifiedAnswer'],
            ],
            BehaviorCode::TRANSLATION => [
                'class' => 'common\base\behaviors\TranslationBehavior',
                'attributes' => ['question', 'htmlAnswer', 'purifiedAnswer'],
            ],
            BehaviorCode::AUDIT => [
                'class' => 'common\base\audit\behaviors\AuditTrailBehavior',
                'ignored' => ['purifiedAnswer']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%faq}}';
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'question',
            'answer' => 'purifiedAnswer',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position'], 'default', 'value' => 999],
            [['type'], 'required'],
            [['question', 'htmlAnswer', 'purifiedAnswer', 'categoryIds'], 'string'],
            [['type'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'type' => Yii::t('model', 'faq.type'),
            'question' => Yii::t('model', 'faq.question'),
            'htmlAnswer' => Yii::t('model', 'faq.htmlAnswer'),
            'purifiedAnswer' => Yii::t('model', 'faq.purifiedAnswer'),
            'categoryIds' => Yii::t('model', 'faq.categoryIds'),
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
     * @return FaqQuery|ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FaqQuery(get_called_class());
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        $this->purifiedAnswer = $this->htmlAnswer;
        return parent::beforeValidate();
    }
}
