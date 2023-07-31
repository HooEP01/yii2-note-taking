<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;
use InvalidArgumentException;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[Image]].
 *
 * @see Image
 */
class ImageQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function hasImage()
    {
        return $this->andWhere(['NOT', [$this->getColumnName('src') => null]]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function ownerType($value)
    {
        return $this->andWhere([$this->getColumnName('ownerType') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function ownerKey($value)
    {
        return $this->andWhere([$this->getColumnName('ownerKey') => $value]);
    }

    /**
     * @param string|\yii\base\Model $value
     * @return $this
     */
    public function owner($value)
    {
        if ($value instanceof ActiveRecord) {
            $type = $value->tableName();
            $key = implode(',', $value->getPrimaryKey(true));

            return $this->ownerType($type)->ownerKey($key);
        } else {
            throw new InvalidArgumentException('$value must be Instance of yii\db\ActiveRecord');
        }
    }

    /**
     * @param string $value
     * @return ImageQuery
     */
    public function code($value)
    {
        return $this->andWhere([$this->getColumnName('code') => $value]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function variant($value)
    {
        return $this->andWhere([$this->getColumnName('variant') => $value]);
    }

    /**
     * @param string|Image $value
     * @return $this
     */
    public function parent($value)
    {
        if ($value instanceof Image) {
            return $this->andWhere([$this->getColumnName('parentId') => $value->id]);
        }
        return $this->andWhere([$this->getColumnName('parentId') => $value]);
    }

    /**
     * @param string|Image $value
     * @return $this
     */
    public function cloneFrom($value)
    {
        if ($value instanceof Image) {
            return $this->andWhere([$this->getColumnName('cloneId') => $value->id]);
        }
        return $this->andWhere([$this->getColumnName('cloneId') => $value]);
    }

    /**
     * @return $this
     */
    public function original()
    {
        return $this
            ->andWhere([$this->getColumnName('parentId') => null])
            ->andWhere([$this->getColumnName('cloneId') => null]);
    }

    /**
     * without parent
     * @return $this
     */
    public function root()
    {
        return $this->andWhere([$this->getColumnName('parentId') => null]);
    }

    /**
     * without parent
     * @return $this
     */
    public function child()
    {
        return $this->andWhere(['IS NOT', $this->getColumnName('parentId'), null]);
    }

    /**
     * without parent
     * @return $this
     */
    public function system()
    {
        return $this->andWhere([$this->getColumnName('isSystem') => true]);
    }

    /**
     * @return $this
     */
    public function orderByDefault()
    {
        return $this->orderBy([
            $this->getColumnName('position') => SORT_ASC,
            $this->getColumnName('createdAt') => SORT_ASC,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Image[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Image|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
