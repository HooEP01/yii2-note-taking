<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\forms;


use common\base\enum\WalletTransactionType;
use common\base\helpers\IntegerDecimal;
use common\models\Wallet;
use yii\base\Model;

/**
 * Class AdjustWalletForm
 * @property Wallet $wallet
 * @package backend\forms
 */
class AdjustWalletForm extends Model
{
    public $amount;
    public $remark;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['amount', 'remark'], 'required'],
            [['amount'], 'number'],
            [['remark'], 'string'],
        ];
    }

    /**
     * @var Wallet
     */
    private $_wallet;

    /**
     * @param Wallet $wallet
     */
    public function setWallet(Wallet $wallet)
    {
        $this->_wallet = $wallet;
    }

    /**
     * @return Wallet
     */
    public function getWallet()
    {
        return $this->_wallet;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function process()
    {
        if (!$this->validate()) {
            return false;
        }

        $options = [
            'type' => WalletTransactionType::MANUAL,
            'description' => $this->remark,
            'reference' => ['code' => 'backend', 'type' => get_called_class(), 'key' => 'manual'],
        ];

        $amountInteger = IntegerDecimal::factoryFromFloat($this->amount)->getIntegerValue();
        if ($amountInteger > 0) {
            return $this->wallet->increase($amountInteger, $options);
        } elseif ($amountInteger < 0) {
            return $this->wallet->decrease($amountInteger, $options);
        }

        return false;
    }
}