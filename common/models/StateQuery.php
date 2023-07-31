<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[State]].
 *
 * @see State
 */
class StateQuery extends ActiveQuery
{
    /**
     * @param $value
     * @return $this
     */
    public function code($value)
    {
        return $this->andWhere([$this->getColumnName('code') => $value]);
    }

    /**
     * @param $value
     * @return $this
     */
    public function country($value)
    {
        if ($value instanceof Country) {
            $value = $value->code;
        }

        return $this->andWhere([$this->getColumnName('countryCode') => $value]);
    }

    /**
     * @param string|array  $value
     * @return $this
     */
    public function name($value)
    {
        return $this->andWhere([$this->getColumnName('name') => $value]);
    }

    /**
     * @return $this
     */
    public function orderByDefault()
    {
        return $this->orderBy([
            $this->getColumnName('position') => SORT_ASC,
            $this->getColumnName('code') => SORT_ASC,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return State[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return State|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
