<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[UserPhone]].
 *
 * @see UserPhone
 */
class UserPhoneQuery extends ActiveQuery
{
    /**
     * @param string $value
     * @return $this
     */
    public function prefix($value)
    {
        return $this->andWhere([$this->getColumnName('prefix') => $value]);
    }
    /**
     * @param string $value
     * @return $this
     */
    public function number($value)
    {
        return $this->andWhere([$this->getColumnName('number') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function complete($value)
    {
        return $this->andWhere([$this->getColumnName('complete') => $value]);
    }

    /**
     * @param User|string $value
     * @return $this
     */
    public function user($value)
    {
        if ($value instanceof User) {
            return $this->andWhere([$this->getColumnName('userId') => $value->id]);
        }

        return $this->andWhere([$this->getColumnName('userId') => $value]);
    }

    /**
     * {@inheritdoc}
     * @return UserPhone[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserPhone|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
