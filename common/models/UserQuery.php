<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 */
class UserQuery extends ActiveQuery
{
    /**
     * @param $value
     * @return $this
     */
    public function type($value)
    {
        return $this->andWhere([$this->getColumnName('type') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function authKey($value)
    {
        return $this->andWhere([$this->getColumnName('authKey') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function username($value)
    {
        return $this->andWhere([$this->getColumnName('username') => $value]);
    }

    /**
     * @param string $value
     * @return UserQuery
     */
    public function passwordResetToken($value)
    {
        return $this->andWhere([$this->getColumnName('passwordResetToken') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function referrerCode($value)
    {
        return $this->andWhere([$this->getColumnName('referrerCode') => $value]);
    }

    /**
     * {@inheritdoc}
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
