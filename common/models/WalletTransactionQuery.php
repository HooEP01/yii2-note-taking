<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[WalletTransaction]].
 *
 * @see WalletTransaction
 */
class WalletTransactionQuery extends ActiveQuery
{
    /**
     * @param $value
     * @return $this
     */
    public function wallet($value)
    {
        if ($value instanceof Wallet) {
            $value = $value->id;
        }

        return $this->andWhere([$this->getColumnName('walletId') => $value]);
    }

    /**
     * {@inheritdoc}
     * @return WalletTransaction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return WalletTransaction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
