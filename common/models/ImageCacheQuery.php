<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;
use common\base\enum\ImageCacheStatus;

/**
 * This is the ActiveQuery class for [[ImageCache]].
 *
 * @see ImageCache
 */
class ImageCacheQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function ready()
    {
        return $this->status(ImageCacheStatus::READY);
    }

    /**
     * @return $this
     */
    public function purging()
    {
        return $this->status(ImageCacheStatus::PURGING);
    }

    /**
     * @param array|string $value
     * @return $this
     */
    public function status($value)
    {
        return $this->andWhere([$this->getColumnName('status') => $value]);
    }

    /**
     * @param string|Image|array $value
     * @return $this
     */
    public function image($value)
    {
        if ($value instanceof Image) {
            return $this->andWhere([$this->getColumnName('imageId') => $value->id]);
        }

        return $this->andWhere([$this->getColumnName('imageId') => $value]);
    }

    /**
     * @param $value
     * @return $this
     */
    public function width($value)
    {
        return $this->andWhere([$this->getColumnName('width') => $value]);
    }

    /**
     * @param $value
     * @return $this
     */
    public function height($value)
    {
        return $this->andWhere([$this->getColumnName('height') => $value]);
    }

    /**
     * {@inheritdoc}
     * @return ImageCache[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ImageCache|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
