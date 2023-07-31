<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;
use InvalidArgumentException;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[Wallet]].
 *
 * @see Wallet
 */
class WalletQuery extends ActiveQuery
{
    /**
     * @param $value
     * @return $this
     */
    public function currency($value)
    {
        if ($value instanceof Currency) {
            $value = $value->code;
        }

        return $this->andWhere([$this->getColumnName('currencyCode') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function ownerType($value)
    {
        return $this->andWhere([$this->getColumnName('ownerType') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function ownerKey($value)
    {
        return $this->andWhere([$this->getColumnName('ownerKey') => $value]);
    }

    /**
     * @param string|\yii\base\Model $value
     * @return $this
     */
    public function owner($value)
    {
        if ($value instanceof ActiveRecord) {
            $type = $value->tableName();
            $key = implode(',', $value->getPrimaryKey(true));

            return $this->ownerType($type)->ownerKey($key);
        } else {
            throw new InvalidArgumentException('$value must be Instance of yii\db\ActiveRecord');
        }
    }

    /**
     * {@inheritdoc}
     * @return Wallet[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Wallet|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
