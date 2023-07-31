<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\db;

use common\base\DateTime;
use common\base\helpers\IntegerDecimal;

/**
 * Class ActiveQuery
 * @package common\base\db;
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @var string
     */
    private $__alias;

    /**
     * @param int|array $value
     * @return $this
     */
    public function id($value)
    {
        //cast column to text
        return $this->andWhere([$this->getColumnName('id') . '::text' => $value]);
    }

    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere([$this->getColumnName('isActive') => true]);
    }

    /**
     * @param string $column
     * @param string $value e.g. 38.5
     * @return array|false
     */
    public function getIntegerDecimalCondition($column, $value)
    {
        if (is_null($value) || (is_string($value) && empty($value))) {
            return false;
        }

        $value = IntegerDecimal::factoryFromFloat($value);
        if (strpos($column, '[') === false && strpos($column, ']') === false) {
            $column = $this->getColumnName($column);
        }

        $search = (string) $value->getIntegerValue();
        $search = rtrim($search, '0');

        return ['ILIKE', $column . '::text', $search];
    }

    /**
     * @param string $column
     * @param string $range e.g. "2020-11-19 00:00 - 2020-12-30 23:59"
     * @return array|false
     */
    public function getDataRangeCondition($column, $range)
    {
        if (!empty($range) && strlen($range) === 35) {
            list($fromDateTime, $toDateTime) = explode(' - ', $range);
            if (strlen($fromDateTime) === strlen($toDateTime)) {
                $fromDateTime = (new DateTime($fromDateTime . ':00'))->local();
                $toDateTime = (new DateTime($toDateTime . ':59'))->local();

                return ['BETWEEN', $column, $fromDateTime->formatToISO8601(), $toDateTime->formatToISO8601()];
            }
        }
        return false;
    }

    /**
     * @param string $column
     * @return string
     */
    protected function getColumnName($column)
    {
        $alias = $this->getAlias();
        if (!empty($alias)) {
            return $alias . '.[[' . $column . ']]';
        }

        return '[[' . $column . ']]';
    }

    /**
     * @inheritdoc
     */
    public function alias($alias)
    {
        $this->setAlias($alias);
        return parent::alias($alias);
    }

    /**
     * @param string $value
     */
    protected function setAlias($value)
    {
        $this->replaceAlias($this->where, $this->getAlias(), $value);
        $this->replaceAlias($this->orderBy, $this->getAlias(), $value);

        $this->__alias = $value;
    }

    /**
     * @param string|array $where
     * @param string $from
     * @param string $to
     */
    protected function replaceAlias(&$where, $from, $to)
    {
        if (is_array($where)) {
            foreach ($where as $key => &$value) {
                if (is_string($key) && strpos($key, $from . '.[[') !== false) {
                    $newKey = str_replace($from . '.[[', $to . '.[[', $key);
                    $where[$newKey] = $value;
                    unset($where[$key]);
                }

                if (is_array($value)) {
                    $this->replaceAlias($value, $from, $to);
                } elseif (is_string($value) && strpos($value, $from . '.[[') !== false) {
                    $where[$key] = str_replace($from . '.[[', $to . '.[[', $value);
                }

                unset($value);//clean up pointer reference
            }
        }
    }

    /**
     * @return string
     */
    protected function getAlias()
    {
        if (isset($this->__alias)) {
            return $this->__alias;
        }

        /* @var $modelClass \yii\db\ActiveRecord */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();

        foreach ((array) $this->from as $key => $table) {
            if ($table === $tableName) {
                if (is_string($key)) {
                    return $this->__alias = $key;
                }
            }
        }

        return $tableName;
    }
}
