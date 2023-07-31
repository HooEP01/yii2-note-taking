<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use Yii;

/**
 * This is the model class for table "{{%state}}".
 *
 * @property string $code
 * @property string $name
 * @property string $shortName
 * @property string|null $imageId
 * @property string $countryCode
 * @property int|null $position
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property City[] $cities
 * @property Country $country
 * @property Image $image
 */
class State extends ActiveRecord
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
                'stripCleanAttributes' => ['code', 'name', 'shortName']
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
        return '{{%state}}';
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
            [['code', 'name', 'shortName', 'countryCode'], 'required'],
            [['imageId'], 'default', 'value' => null],
            [['imageId'], 'string'],
            [['code'], 'string', 'max' => 25],
            [['name'], 'string', 'max' => 254],
            [['shortName'], 'string', 'max' => 128],
            [['countryCode'], 'string', 'max' => 8],
            [['code'], 'unique'],
            [['countryCode'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['countryCode' => 'code']],
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
            'code' => Yii::t('model', 'state.code'),
            'name' => Yii::t('model', 'state.name'),
            'shortName' => Yii::t('model', 'state.shortName'),
            'imageId' => Yii::t('model', 'state.imageId'),
            'countryCode' => Yii::t('model', 'state.countryCode'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \yii\db\ActiveQuery|CountryQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['code' => 'countryCode']);
    }

    /**
     * @return \yii\db\ActiveQuery|CityQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::class, ['stateCode' => 'code']);
    }

    /**
     * {@inheritdoc}
     * @return StateQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StateQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function options()
    {
        $key = 'state-options-v1';
        return Yii::$app->cache->getOrSet($key, function () {
            $options = [];
            $query = self::find()
                ->active()
                ->joinWith([
                    'country c' => function (CountryQuery $countryQuery) {
                        return $countryQuery->active()->orderByDefault();
                    }
                ])
                ->orderByDefault();

            foreach ($query->all() as $item) {
                $options[$item->country->name][$item->code] = sprintf('[%s] %s', $item->code, $item->name);
            }

            return $options;
        });
    }
}
