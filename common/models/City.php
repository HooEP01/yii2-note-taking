<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\helpers\ArrayHelper;
use common\jobs\SearchSuggestionReindex;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%city}}".
 *
 * @property string $id
 * @property string $name
 * @property string|null $shortName
 * @property string|null $imageId
 * @property string $stateCode
 * @property string $countryCode
 * @property int|null $position
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property Country $country
 * @property Image $image
 * @property State $state
 */
class City extends ActiveRecord
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
                'stripCleanAttributes' => ['name', 'shortName', 'position']
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
        return '{{%city}}';
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'id' => 'shortUuid',
            'name',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position'], 'default', 'value' => 999],
            [['name', 'shortName'], 'trim'],
            [['name', 'stateCode', 'countryCode'], 'required'],
            [['imageId'], 'string'],
            [['name'], 'string', 'max' => 254],
            [['shortName'], 'string', 'max' => 128],
            [['stateCode'], 'string', 'max' => 25],
            [['countryCode'], 'string', 'max' => 8],
            [['countryCode'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['countryCode' => 'code']],
            [['imageId'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['imageId' => 'id']],
            [['stateCode'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['stateCode' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'name' => Yii::t('model', 'city.name'),
            'shortName' => Yii::t('model', 'city.shortName'),
            'imageId' => Yii::t('model', 'city.imageId'),
            'stateCode' => Yii::t('model', 'city.stateCode'),
            'countryCode' => Yii::t('model', 'city.countryCode'),
            'position' => Yii::t('model', 'common.position'),
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
    public function getDisplayName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDropdownOptionName()
    {
        $name = Html::tag('strong', $this->country->name, ['class' => 'text-purple']) . ' &rArr; ';
        $name .= Html::tag('span', $this->state->name, ['class' => 'text-green']) . ' &rArr; ';
        $name .= Html::tag('span', $this->getDisplayName());

        // $name .= ' [' . Html::tag('strong', 'ID: ') . Html::tag('i', $this->getShortUuid()) . ']';

        return $name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(Address::className(), ['cityId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['code' => 'countryCode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['code' => 'stateCode']);
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->state) {
            $this->countryCode = $this->state->countryCode;
        }

        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        try {
            Yii::$app->pipeline->push(SearchSuggestionReindex::factory($this));
        } catch (\Exception $e) {
            Yii::error($e);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * {@inheritdoc}
     * @return CityQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CityQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function options()
    {
        $key = 'city-options-v1';
        return Yii::$app->cache->getOrSet($key, function () {
            $options = [];
            $query = self::find()
                ->active()
                ->joinWith([
                    'state s' => function (StateQuery $stateQuery) {
                        return $stateQuery->active()->orderByDefault();
                    }
                ]);

            foreach ($query->all() as $item) {
                $options[$item->state->name][$item->id] = $item->name;
            }

            return $options;
        });
    }
}
