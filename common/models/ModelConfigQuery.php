<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[ModelConfig]].
 *
 * @see ModelConfig
 */
class ModelConfigQuery extends ActiveQuery
{
    /**
     * @param string|array $value
     * @return $this
     */
    public function name($value)
    {
        return $this->andWhere([$this->getColumnName('name') => $value]);
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
     * @return $this
     */
    public function ownerKey($value)
    {
        return $this->andWhere([$this->getColumnName('ownerKey') => $value]);
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
     * {@inheritdoc}
     * @return ModelConfig[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ModelConfig|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
