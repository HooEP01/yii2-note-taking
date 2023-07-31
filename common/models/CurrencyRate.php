<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\helpers\IntegerDecimal;
use Yii;

/**
 * This is the model class for table "{{%currency_rate}}".
 *
 * @property string $id
 * @property string $sourceCurrencyCode
 * @property string $targetCurrencyCode
 * @property int $conversionRate
 * @property int $precision depend the precision, if 6, then 0.23 = 230000
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property Currency $sourceCurrency
 * @property Currency $targetCurrency
 */
class CurrencyRate extends ActiveRecord
{
    public $conversionRateValue;

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
                'stripCleanAttributes' => ['sourceCurrencyCode', 'targetCurrencyCode', 'precision', 'conversionRate'],
            ],
            BehaviorCode::INTEGER_DECIMAL => [
                'class' => 'common\base\behaviors\IntegerDecimalBehavior',
                'attributes' => [
                    'conversionRate',
                ]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%currency_rate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['precision'], 'default', 'value' => 6],
            [['conversionRateValue'], 'default', 'value' => 1],
            [['conversionRateValue'], 'number', 'min' => 0.000001],
            [['sourceCurrencyCode', 'targetCurrencyCode'], 'required'],
            [['precision', 'conversionRate'], 'integer'],
            [['sourceCurrencyCode', 'targetCurrencyCode'], 'string', 'max' => 8],
            [['sourceCurrencyCode', 'targetCurrencyCode'], 'unique', 'targetAttribute' => ['sourceCurrencyCode', 'targetCurrencyCode']],
            [['sourceCurrencyCode'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['sourceCurrencyCode' => 'code']],
            [['targetCurrencyCode'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['targetCurrencyCode' => 'code']],
            [['targetCurrencyCode'], 'compare', 'compareAttribute' => 'sourceCurrencyCode', 'operator' => '!==']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'sourceCurrencyCode' => Yii::t('model', 'currency_rate.sourceCurrencyCode'),
            'targetCurrencyCode' => Yii::t('model', 'currency_rate.targetCurrencyCode'),
            'precision' => Yii::t('model', 'common.precision'),
            'conversionRate' => Yii::t('model', 'currency_rate.conversionRate'),
            'conversionRateValue' => Yii::t('model', 'currency_rate.conversionRate'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery|CurrencyQuery
     */
    public function getSourceCurrency()
    {
        return $this->hasOne(Currency::className(), ['code' => 'sourceCurrencyCode']);
    }

    /**
     * @return \yii\db\ActiveQuery|CurrencyQuery
     */
    public function getTargetCurrency()
    {
        return $this->hasOne(Currency::className(), ['code' => 'targetCurrencyCode']);
    }

    /**
     * {@inheritdoc}
     * @return CurrencyRateQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CurrencyRateQuery(get_called_class());
    }

    /**
     * @param $source
     * @param $target
     * @return IntegerDecimal|int
     */
    public static function getRateIntegerDecimal($source, $target)
    {
        if ($source === $target) {
            $rate = 1;
        } else {
            $model = self::find()
                ->source($source)
                ->target($target)
                ->active()
                ->orderByDefault()
                ->limit(1)
                ->one();

            $rate = $model ? $model->conversionRate : -1;
        }

        return IntegerDecimal::factoryFromInteger($rate);
    }
}
