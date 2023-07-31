<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "{{%country}}".
 *
 * @property string $code
 * @property string $name
 * @property string $shortName
 * @property string|null $imageId
 * @property string|null $iso3
 * @property string|null $numCode
 * @property string|null $telCode
 * @property string|null $currencyCode
 * @property string|null $defaultStateCode
 * @property bool|null $isStateRequired
 * @property bool|null $isPostcodeRequired
 * @property int|null $position
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property City[] $cities
 * @property Image $image
 * @property State[] $states
 */
class Country extends ActiveRecord
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
                'stripCleanAttributes' => ['code', 'name', 'shortName', 'iso3', 'numCode', 'telCode', 'position'],
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
        return '{{%country}}';
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'code',
            'name',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name', 'currencyCode'], 'required'],
            [['position'], 'default', 'value' => 999],
            [['imageId'], 'default', 'value' => null],
            [['imageId'], 'string'],
            [['isStateRequired', 'isPostcodeRequired'], 'boolean'],
            [['code', 'iso3', 'numCode', 'telCode', 'currencyCode'], 'string', 'max' => 8],
            [['name'], 'string', 'max' => 254],
            [['shortName'], 'string', 'max' => 254],
            [['defaultStateCode'], 'string', 'max' => 25],
            [['code'], 'unique'],
            [['imageId'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['imageId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('model', 'country.code'),
            'name' => Yii::t('model', 'country.name'),
            'shortName' => Yii::t('model', 'country.shortName'),
            'imageId' => Yii::t('model', 'country.imageId'),
            'iso3' => Yii::t('model', 'country.iso3'),
            'numCode' => Yii::t('model', 'country.numCode'),
            'telCode' => Yii::t('model', 'country.telCode'),
            'currencyCode' => Yii::t('model', 'country.currencyCode'),
            'defaultStateCode' => Yii::t('model', 'country.defaultStateCode'),
            'isStateRequired' => Yii::t('model', 'country.isStateRequired'),
            'isPostcodeRequired' => Yii::t('model', 'country.isPostcodeRequired'),
            'position' => Yii::t('model', 'common.position'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['countryCode' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStates()
    {
        return $this->hasMany(State::className(), ['countryCode' => 'code']);
    }

    /**
     * {@inheritdoc}
     * @return CountryQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CountryQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function options()
    {
        $key = 'country-options-v1';
        return Yii::$app->cache->getOrSet($key, function () {
            $query = self::find()
                ->active()
                ->orderByDefault();

            return ArrayHelper::map($query->all(), 'code', 'name');
        });
    }
}
