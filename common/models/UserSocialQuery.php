<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;
use common\base\enum\SocialChannel;

/**
 * This is the ActiveQuery class for [[UserSocial]].
 *
 * @see UserSocial
 */
class UserSocialQuery extends ActiveQuery
{
    /**
     * @param User|int $value
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
     * @param string $value
     * @return $this
     */
    public function token($value)
    {
        return $this->andWhere([$this->getColumnName('token') => $value]);
    }

    /**
     * @return $this
     */
    public function verified()
    {
        return $this->andWhere([$this->getColumnName('isVerified') => true]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function channelId($value)
    {
        return $this->andWhere([$this->getColumnName('channelId') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function channel($value)
    {
        return $this->andWhere([$this->getColumnName('channel') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function facebook($value = null)
    {
        $this->channel(SocialChannel::FACEBOOK);
        if (!empty($value)) {
            return $this->channelId($value);
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function google($value = null)
    {
        $this->channel(SocialChannel::GOOGLE);
        if (!empty($value)) {
            return $this->channelId($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return UserSocial[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserSocial|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
