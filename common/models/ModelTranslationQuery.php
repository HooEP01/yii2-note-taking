<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\models;

use common\base\db\ActiveQuery;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[ModelTranslation]].
 *
 * @see ModelTranslation
 */
class ModelTranslationQuery extends ActiveQuery
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
    public function language($value)
    {
        return $this->andWhere([$this->getColumnName('language') => $value]);
    }

    /**
     * @param string|array $value
     * @return $this
     */
    public function message($value)
    {
        if (is_array($value)) {
            return $this->andWhere(['IN', $this->getColumnName('message'), $value]);
        }

        return $this->andWhere([$this->getColumnName('message') => $value]);
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
     * @param ActiveRecord $value
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
     * {@inheritdoc}
     * @return ModelTranslation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ModelTranslation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
