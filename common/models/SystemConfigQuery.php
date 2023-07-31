<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[SystemConfig]].
 *
 * @see SystemConfig
 */
class SystemConfigQuery extends ActiveQuery
{
    /**
     * @param string $value
     * @return $this
     */
    public function name($value)
    {
        return $this->andWhere([$this->getColumnName('name') => $value]);
    }

    /**
     * {@inheritdoc}
     * @return SystemConfig[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SystemConfig|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
