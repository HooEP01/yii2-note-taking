<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[UserEmail]].
 *
 * @see UserEmail
 */
class UserEmailQuery extends ActiveQuery
{
    /**
     * @param $value
     * @return $this
     */
    public function user($value)
    {
        if ($value instanceof User) {
            $value = $value->id;
        }

        return $this->andWhere([$this->getColumnName('userId') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function email($value)
    {
        return $this->address($value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function address($value)
    {
        return $this->andWhere([$this->getColumnName('address') => $value]);
    }

    /**
     * {@inheritdoc}
     * @return UserEmail[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserEmail|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
