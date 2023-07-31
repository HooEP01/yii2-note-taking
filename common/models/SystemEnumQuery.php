<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;
use common\base\enum\SystemEnumType;

/**
 * This is the ActiveQuery class for [[SystemEnum]].
 *
 * @see SystemEnum
 */
class SystemEnumQuery extends ActiveQuery
{
    /**
     * @param string $value
     * @return $this
     */
    public function code($value)
    {
        return $this->andWhere([$this->getColumnName('code') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function type($value)
    {
        return $this->andWhere([$this->getColumnName('type') => $value]);
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
     * @return SystemEnum[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SystemEnum|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
