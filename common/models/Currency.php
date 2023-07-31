<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\CurrencyCode;
use common\base\enum\CurrencyFormat;
use Yii;

/**
 * This is the model class for table "{{%currency}}".
 *
 * @property string $code
 * @property string $name
 * @property string|null $shortName
 * @property string|null $imageId
 * @property string $symbol
 * @property string $format
 * @property int $precision
 * @property string $decimalPoint
 * @property string $thousandsSeparator
 * @property int|null $position
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property CurrencyRate[] $currencyRates
 * @property CurrencyRate[] $currencyRates0
 * @property Image $image
 * @property Currency[] $sourceCurrencyCodes
 * @property Currency[] $targetCurrencyCodes
 */
class Currency extends ActiveRecord
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
                'stripCleanAttributes' => ['code', 'name', 'shortName', 'symbol', 'format'],
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
        return '{{%currency}}';
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'code',
            'name',
            'symbol',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name', 'symbol'], 'required'],
            [['imageId'], 'default', 'value' => null],
            [['imageId'], 'string'],
            [['precision'], 'integer'],
            [['code', 'symbol', 'decimalPoint', 'thousandsSeparator'], 'string', 'max' => 8],
            [['name'], 'string', 'max' => 254],
            [['shortName'], 'string', 'max' => 128],
            [['format'], 'string', 'max' => 64],
            [['code'], 'unique'],
            [['imageId'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['imageId' => 'id']],
            [['position'], 'default', 'value' => 999],
            [['format'], 'default', 'value' => CurrencyFormat::SYMBOL_VALUE],
            [['precision'], 'default', 'value' => 2],
            [['decimalPoint'], 'default', 'value' => '.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('model', 'common.code'),
            'name' => Yii::t('model', 'common.name'),
            'shortName' => Yii::t('model', 'common.shortName'),
            'imageId' => Yii::t('model', 'common.imageId'),
            'symbol' => Yii::t('model', 'currency.symbol'),
            'format' => Yii::t('model', 'common.format'),
            'precision' => Yii::t('model', 'common.precision'),
            'decimalPoint' => Yii::t('model', 'currency.decimalPoint'),
            'thousandsSeparator' => Yii::t('model', 'currency.thousandsSeparator'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return CurrencyQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CurrencyQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function options()
    {
        $key = 'currency-options-v1';
        return Yii::$app->cache->getOrSet($key, function () {
            $options = [];
            $query = self::find()
                ->active()
                ->orderByDefault();

            foreach ($query->all() as $item) {
                $options[$item->code] = sprintf('[%s] %s', $item->symbol, $item->name);
            }

            return $options;
        });
    }

    /**
     * @param string $code
     * @return array
     */
    public static function formatOptions($code = CurrencyCode::AUSTRALIAN_DOLLAR)
    {
        $model = self::findOne($code);
        if (!$model) {
            return [];
        }

        return [
            'decimals' => $model->precision,
            'currencySymbol' => $model->symbol,
            'format' => $model->format,
        ];
    }
}
