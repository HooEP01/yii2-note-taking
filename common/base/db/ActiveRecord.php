<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */


namespace common\base\db;

use common\base\DateTime;
use common\base\helpers\UuidHelper;
use common\base\traits\RuntimeCache;
use Yii;

/**
 * Class ActiveRecord
 * @property string $shortUuid
 * @package common\base\db;
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    use RuntimeCache;

    public function getShortUuid()
    {
        $pk = $this->getPrimaryKey(true);
        return UuidHelper::encodeShort(current($pk));
    }

    /**
     * @return string|null
     */
    public function getFirstErrorMessage()
    {
        if (!$this->getFirstErrors()) {
            return null;
        }

        return array_values($this->getFirstErrors())[0];
    }

    /**
     * @return bool
     */
    public function softDelete(): bool
    {
        if ($this->hasAttribute('isActive')) {
            $this->setAttribute('isActive', false);

            if ($this->hasAttribute('deletedBy')) {
                $userId = Yii::$app->user->isGuest ? '00000000-0000-0000-0000-000000000000' : Yii::$app->user->id;
                if (YII_CONSOLE_MODE) {
                    $userId = '11111111-1111-1111-1111-111111111111'; //console mode
                }
                $this->setAttribute('deletedBy', $userId);
            }

            if ($this->hasAttribute('deletedAt')) {
                $this->setAttribute('deletedAt', DateTime::getCurrentDateTimeISO8601());
            }

            return $this->save(false);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function softRestore(): bool
    {
        if ($this->hasAttribute('isActive')) {
            $this->setAttribute('isActive', true);
            if ($this->hasAttribute('deletedBy')) {
                $this->setAttribute('deletedBy', null);
            }
            if ($this->hasAttribute('deletedAt')) {
                $this->setAttribute('deletedAt', null);
            }

            return $this->save(false);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function toggleActive(): bool
    {
        if ($this->hasAttribute('isActive')) {
            $value = (bool) $this->getAttribute('isActive');
            $this->setAttribute('isActive', !$value);
            return $this->save(false);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getIsActive(): bool
    {
        if ($this->hasAttribute('isActive')) {
            return (bool) $this->getAttribute('isActive');
        }

        return false;
    }

    /**
     * @return array
     */
    public function extraFields()
    {
        return [];
    }

    /**
     * @inheritdoc
     * @param array $fields
     * @param array $expand
     * @return array
     */
    protected function resolveFields(array $fields, array $expand): array
    {
        $result = [];

        foreach ($this->fields() as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }
            if (empty($fields) || in_array($field, $fields, true)) {
                $result[$field] = $definition;
            }
        }

        if (empty($expand)) {
            return $result;
        }

        foreach ($this->extraFields() as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }
            if (in_array('*', $expand, true) || in_array($field, $expand, true)) {
                $result[$field] = $definition;
            }
        }

        return $result;
    }
}

