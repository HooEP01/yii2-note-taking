<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\helpers\ArrayHelper;
use common\base\helpers\IntegerDecimal;
use ReflectionClass;
use Yii;
use yii\base\InvalidCallException;

/**
 * This is the model class for table "{{%wallet}}".
 *
 * @property string $id
 * @property string $ownerType Table name of the model or class of owner
 * @property string $ownerKey The string representation of pk or id
 * @property string $currencyCode
 * @property int $precision
 * @property int $cacheBalance depend the decimal_precision, if 2, then 100 = 10000
 * @property int $cacheWithdrawable depend the decimal_precision, if 2, then 100 = 10000
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property Currency $currency
 * @property WalletTransaction[] $walletTransactions
 */
class Wallet extends ActiveRecord
{
    public $cacheBalanceValue;
    public $cacheWithdrawableValue;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            BehaviorCode::BLAMEABLE => [
                'class' => 'common\base\behaviors\BlameableBehavior',
            ],
            BehaviorCode::INTEGER_DECIMAL => [
                'class' => 'common\base\behaviors\IntegerDecimalBehavior',
                'attributes' => [
                    'cacheBalance',
                    'cacheWithdrawable',
                ]
            ],
            BehaviorCode::AUDIT => [
                'class' => 'common\base\audit\behaviors\AuditTrailBehavior',
                'ignored' => ['cacheBalance', 'cacheWithdrawable']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wallet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ownerType', 'ownerKey', 'currencyCode'], 'required'],
            [['cacheBalanceValue', 'cacheWithdrawableValue'], 'default', 'value' => 0],
            [['precision'], 'default', 'value' => 2],
            [['precision', 'cacheBalance', 'cacheWithdrawable'], 'integer'],
            [['ownerType'], 'string', 'max' => 128],
            [['ownerKey'], 'string', 'max' => 192],
            [['currencyCode'], 'string', 'max' => 8],
            [['ownerType', 'ownerKey', 'currencyCode'], 'unique', 'targetAttribute' => ['ownerType', 'ownerKey', 'currencyCode']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'ownerType' => Yii::t('model', 'common.ownerType'),
            'ownerKey' => Yii::t('model', 'common.ownerKey'),
            'currencyCode' => Yii::t('model', 'common.currencyCode'),
            'precision' => Yii::t('model', 'common.precision'),
            'magnifier' => Yii::t('model', 'common.magnifier'),
            'cacheBalance' => Yii::t('model', 'wallet.cacheBalance'),
            'cacheWithdrawable' => Yii::t('model', 'wallet.cacheWithdrawable'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return bool
     */
    public function getIsUsable()
    {
        return (bool) ($this->getBalance() > 0);
    }

    /**
     * @return null|User
     */
    public function getOwnerModel()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $maps = $this->getOwnerClassMaps();

            if (isset($maps[$this->ownerType])) {
                $class = $maps[$this->ownerType];
                $model = call_user_func([$class, 'findOne'], $this->ownerKey);
                return $model;
            }

            return null;
        });
    }

    /**
     * @return array
     */
    protected function getOwnerClassMaps()
    {
        return $this->getOrSetRuntimeData(__METHOD__, function () {
            $class = new ReflectionClass($this);
            $namespace = $class->getNamespaceName();

            $maps = [];
            foreach (['User', 'Merchant'] as $name) {
                $className = sprintf('%s\%s', $namespace, $name);
                if (class_exists($className)) {
                    $tableName = call_user_func([$className, 'tableName']);
                    $maps[$tableName] = $className;
                }
            }
            return $maps;
        });
    }

    /**
     * @param bool $useCache
     * @return IntegerDecimal
     */
    public function getUsableIntegerDecimal($useCache = true)
    {
        return IntegerDecimal::factoryFromInteger($this->getUsable($useCache), $this->getPrecision());
    }

    /**
     * @param bool $useCache
     * @return int
     */
    public function getUsable($useCache = true)
    {
        return $this->getBalance($useCache);
    }

    /**
     * @param bool $useCache
     * @return int
     */
    public function getBalance($useCache = true)
    {
        if ($useCache) {
            return (int) $this->cacheBalance;
        }

        return $this->getBalanceFromTransactions();
    }

    /**
     * @param bool $useCache
     * @return IntegerDecimal
     */
    public function getBalanceIntegerDecimal($useCache = true)
    {
        return IntegerDecimal::factoryFromInteger($this->getBalance($useCache), $this->getPrecision());
    }

    /**
     * @param bool $useCache
     * @return int
     */
    public function getWithdrawable($useCache = true)
    {
        if ($useCache) {
            return (int) $this->cacheWithdrawable;
        }

        return $this->getWithdrawableFromTransactions();
    }

    /**
     * @param bool $useCache
     * @return IntegerDecimal
     */
    public function getWithdrawableIntegerDecimal($useCache = true)
    {
        return IntegerDecimal::factoryFromInteger($this->getWithdrawable($useCache), $this->getPrecision());
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return (int) $this->precision;

    }

    /**
     * @return array
     */
    public function getPriceFormatOptions()
    {
        return [
            'currencySymbol' => $this->currency->symbol,
            'format' => $this->currency->format,
            'precision' => $this->getPrecision(),
        ];
    }

    /**
     * @param int|IntegerDecimal $amount
     * @param array              $options
     * @return boolean
     * @throws \Exception
     */
    public function increase($amount, $options = [])
    {
        if ($amount instanceof IntegerDecimal) {
            $amount = $amount->getIntegerValue();
        }

        $amount = abs($amount);//always positive
        return $this->adjust($amount, $options);
    }

    /**
     * @param int|IntegerDecimal $amount
     * @param array              $options
     * @return boolean
     * @throws \Exception
     */
    public function decrease($amount, $options = [])
    {
        if ($amount instanceof IntegerDecimal) {
            $amount = $amount->getIntegerValue();
        }

        $amount = abs($amount) * -1;//always negative
        return $this->adjust($amount, $options);
    }

    /**
     * @param int $amount
     * @param array $options
     * @return boolean
     * @throws \Exception
     */
    protected function adjust($amount, $options = [])
    {
        $amount = (int) $amount;

        if (($type = ArrayHelper::getValue($options, 'type')) === null || empty($type)) {
            throw new InvalidCallException('type must be given !');
        }

        if (($description = ArrayHelper::getValue($options, 'description')) === null || empty($description)) {
            throw new InvalidCallException('description must be given !');
        }

        if (($reference = ArrayHelper::getValue($options, 'reference')) === null || empty($reference)) {
            throw new InvalidCallException('reference must be given !');
        }

        if (is_array($reference)) {
            foreach (['code', 'type', 'key'] as $field) {
                if (!isset($reference[$field]) || empty($reference[$field])) {
                    throw new InvalidCallException(sprintf('reference[%s] must be given !', $field));
                }
            }
        } elseif ($reference instanceof ActiveRecord) {
            $reference = [
                'code' => 'model',
                'type' => $reference->tableName(),
                'key' => implode(',', $reference->getPrimaryKey(true))
            ];
        } else {
            throw new InvalidCallException('reference must be an array or ActiveRecord Object !');
        }

        $valid = true;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = new WalletTransaction();
            $model->walletId = $this->id;
            $model->type = $type;
            $model->description = $description;
            $model->precision = $this->precision;
            $model->amount = $amount;
            $model->referenceCode = ArrayHelper::getValue($reference, 'code');
            $model->referenceType = ArrayHelper::getValue($reference, 'type');
            $model->referenceKey = ArrayHelper::getValue($reference, 'key');

            $valid = $valid && $model->save();
            $valid = $valid && $this->recalculate();

            if ($model->hasErrors()) {
                Yii::error($model->errors);
            }
            if ($this->hasErrors()) {
                Yii::error($this->errors);
            }

            $valid ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $valid = false;
            $transaction->rollBack();
            Yii::error($e);
        }

        return $valid;
    }

    /**
     * this function will recalculate the balance
     * @return bool
     */
    public function recalculate()
    {
        $this->cacheBalance = $this->getBalanceFromTransactions();
        $this->cacheWithdrawable = $this->getWithdrawableFromTransactions();

        return $this->save();
    }

    /**
     * @return int
     */
    public function getBalanceFromTransactions()
    {
        $amount = (int) WalletTransaction::find()->alias('t')
            ->select(['balance' => 'SUM([[t]].[[amount]])'])
            ->wallet($this)
            ->active()
            ->scalar();

        return $amount;
    }

    /**
     * @return int
     */
    public function getWithdrawableFromTransactions()
    {
        $amount = $this->getBalanceFromTransactions();
        if ($amount < 0) {
            $amount = 0;
        }

        return $amount;
    }

    /**
     * @param \yii\db\ActiveRecord $model
     */
    public function setOwnerModel(yii\db\ActiveRecord $model)
    {
        $this->ownerType = $model->tableName();
        $this->ownerKey = implode(',', $model->getPrimaryKey(true));
    }

    /**
     * @return \yii\db\ActiveQuery|CurrencyQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['code' => 'currencyCode']);
    }

    /**
     * @return \yii\db\ActiveQuery|WalletTransactionQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(WalletTransaction::class, ['walletId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return WalletQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WalletQuery(get_called_class());
    }

    /**
     * @param User|ActiveRecord $value
     * @return static
     */
    public static function factory($value)
    {
        $model = new static();
        if ($value instanceof yii\db\ActiveRecord) {
            $model->setOwnerModel($value);
        }

        return $model;
    }
}
