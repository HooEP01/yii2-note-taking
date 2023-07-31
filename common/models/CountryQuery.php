<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Country]].
 *
 * @see Country
 */
class CountryQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function withMobilePrefix()
    {
        return $this->andWhere(['IS NOT', $this->getColumnName('telCode'), null])
            ->andWhere(['!=', $this->getColumnName('telCode'), '']);
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
     * @return Country[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Country|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
