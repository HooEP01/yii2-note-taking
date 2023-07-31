<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use Yii;

/**
 * This is the model class for table "{{%language}}".
 *
 * @property string $code
 * @property string $name
 * @property string|null $shortName
 * @property string|null $imageId
 * @property int|null $position
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property Image $image
 */
class Language extends ActiveRecord
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
                'stripCleanAttributes' => ['name', 'shortName']
            ],
            BehaviorCode::TRANSLATION => [
                'class' => 'common\base\behaviors\TranslationBehavior',
                'attributes' => ['name', 'shortName'],
            ],
            BehaviorCode::IMAGE => [
                'class' => 'common\base\behaviors\ImageBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%language}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['imageId'], 'default', 'value' => null],
            [['imageId'], 'string'],
            [['code'], 'string', 'max' => 8],
            [['name'], 'string', 'max' => 254],
            [['shortName'], 'string', 'max' => 128],
            [['code'], 'unique'],
            [['imageId'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['imageId' => 'id']],
            [['position'], 'default', 'value' => 999],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('model', 'language.code'),
            'name' => Yii::t('model', 'language.name'),
            'shortName' => Yii::t('model', 'language.shortName'),
            'imageId' => Yii::t('model', 'language.imageId'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return LanguageQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LanguageQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function options()
    {
        $key = 'language-options-v1';
        return Yii::$app->cache->getOrSet($key, function () {
            $options = [];
            $query = self::find()
                ->active()
                ->orderByDefault();

            foreach ($query->all() as $item) {
                $options[$item->code] = sprintf('[%s] %s', $item->code, $item->name);
            }

            return $options;
        });
    }
}
