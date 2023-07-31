<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[CurrencyRate]].
 *
 * @see CurrencyRate
 */
class CurrencyRateQuery extends ActiveQuery
{
    /**
     * @param $value
     * @return $this
     */
    public function source($value)
    {
        if ($value instanceof Currency) {
            $value = $value->code;
        }

        return $this->andWhere([$this->getColumnName('sourceCurrencyCode') => $value]);
    }

    /**
     * @param $value
     * @return $this
     */
    public function target($value)
    {
        if ($value instanceof Currency) {
            $value = $value->code;
        }

        return $this->andWhere([$this->getColumnName('targetCurrencyCode') => $value]);
    }

    /**
     * @return $this
     */
    public function orderByDefault()
    {
        return $this->orderBy([
            $this->getColumnName('createdAt') => SORT_DESC
        ]);
    }

    /**
     * {@inheritdoc}
     * @return CurrencyRate[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CurrencyRate|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
