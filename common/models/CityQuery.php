<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[City]].
 *
 * @see City
 */
class CityQuery extends ActiveQuery
{
    /**
     * @param $value
     * @return $this
     */
    public function name($value)
    {
        return $this->andWhere([sprintf('LOWER(%s)', $this->getColumnName('name')) => strtolower(trim($value))]);
    }

    /**
     * @param $value
     * @return $this
     */
    public function state($value)
    {
        if ($value instanceof State) {
            $value = $value->code;
        }

        return $this->andWhere([$this->getColumnName('stateCode') => $value]);
    }

    /**
     * @return $this
     */
    public function orderByDefault()
    {
        return $this->orderBy([
            $this->getColumnName('position') => SORT_ASC,
            $this->getColumnName('name') => SORT_ASC,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return City[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return City|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
