<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\DateTime;
use common\base\db\ActiveRecord;
use common\base\enum\BehaviorCode;
use common\base\enum\WalletTransactionType;
use common\base\helpers\IntegerDecimal;
use common\base\helpers\Json;
use Yii;

/**
 * This is the model class for table "{{%wallet_transaction}}".
 *
 * @property string $id
 * @property string $walletId
 * @property string $type
 * @property string $description the english message
 * @property int $amount depend on the decimal_precision, if 2, then 100 = 10000
 * @property int $precision
 * @property string|null $referenceCode The reference code, e.g. model
 * @property string|null $referenceType e.g. table name
 * @property string|null $referenceKey e.g. the table pk or id
 * @property string|null $data for storing any extra data required
 * @property string|null $translateCategory
 * @property string|null $translateMessage
 * @property string|null $translateData
 * @property string|null $settlementId
 * @property string $createdBy
 * @property string $createdAt
 * @property string $updatedBy
 * @property string $updatedAt
 * @property bool $isActive
 *
 * @property Wallet $wallet
 */
class WalletTransaction extends ActiveRecord
{
    public $amountValue;

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
                'attributes' => ['amount']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wallet_transaction}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['walletId', 'type', 'description', 'amount'], 'required'],
            [['walletId', 'description', 'translateMessage', 'settlementId'], 'string'],
            [['amount', 'precision'], 'default', 'value' => null],
            [['amount', 'precision'], 'integer'],
            [['amountValue'], 'number'],
            [['data', 'translateData'], 'safe'],
            [['type', 'translateCategory'], 'string', 'max' => 64],
            [['referenceCode', 'referenceType'], 'string', 'max' => 128],
            [['referenceKey'], 'string', 'max' => 192],
            [['walletId'], 'exist', 'skipOnError' => true, 'targetClass' => Wallet::className(), 'targetAttribute' => ['walletId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'common.id'),
            'walletId' => Yii::t('model', 'wallet_transaction.walletId'),
            'type' => Yii::t('model', 'common.type'),
            'description' => Yii::t('model', 'common.description'),
            'amount' => Yii::t('model', 'common.amount'),
            'precision' => Yii::t('model', 'common.precision'),
            'referenceCode' => Yii::t('model', 'wallet_transaction.referenceCode'),
            'referenceType' => Yii::t('model', 'wallet_transaction.referenceType'),
            'referenceKey' => Yii::t('model', 'wallet_transaction.referenceKey'),
            'data' => Yii::t('model', 'common.data'),
            'translateCategory' => Yii::t('model', 'wallet_transaction.translateCategory'),
            'translateMessage' => Yii::t('model', 'wallet_transaction.translateMessage'),
            'translateData' => Yii::t('model', 'wallet_transaction.translateData'),
            'settlementId' => Yii::t('model', 'wallet_transaction.settlementId'),
            'createdBy' => Yii::t('model', 'common.createdBy'),
            'createdAt' => Yii::t('model', 'common.createdAt'),
            'updatedBy' => Yii::t('model', 'common.updatedBy'),
            'updatedAt' => Yii::t('model', 'common.updatedAt'),
            'isActive' => Yii::t('model', 'common.isActive'),
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCreatedAtFormatted()
    {
        $datetime = new DateTime($this->createdAt);
        return $datetime->formatToDisplayDateTime();
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return WalletTransactionType::resolve($this->getType());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getPriceFormatOptions()
    {
        return [
            'currencySymbol' => $this->wallet->currency->symbol,
            'precision' => $this->getPrecision(),
        ];
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return (int) $this->amount;
    }

    /**
     * @return IntegerDecimal
     */
    public function getAmountIntegerDecimal()
    {
        return IntegerDecimal::factoryFromInteger($this->getAmount(), $this->getPrecision());
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        if (empty($this->translateCategory) || empty($this->translateMessage)) {
            return $this->description;
        }

        return Yii::t($this->translateCategory, $this->translateMessage, $this->getTranslateData());
    }

    /**
     * @return array
     */
    public function getTranslateData()
    {
        if (empty($this->translateData)) {
            return [];
        }

        return Json::decode($this->translateData);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWallet()
    {
        return $this->hasOne(Wallet::className(), ['id' => 'walletId']);
    }

    /**
     * {@inheritdoc}
     * @return WalletTransactionQuery|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WalletTransactionQuery(get_called_class());
    }
}
