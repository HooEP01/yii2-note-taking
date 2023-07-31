<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\db\pgsql;

use yii\db\ColumnSchemaBuilder as AbstractColumnSchemaBuilder;
use yii\db\Expression;

/**
 * Class ColumnSchemaBuilder
 * @package common\base\db\pgsql
 */
class ColumnSchemaBuilder extends AbstractColumnSchemaBuilder
{
    /**
     * Specify the default SQL expression for the column.
     * @return $this
     * @since 2.0.7
     */
    public function defaultNow(): ColumnSchemaBuilder
    {
        $this->default = new Expression('NOW()');
        return $this;
    }

    /**
     * Returns the category of the column type.
     * @return string a string containing the column type category name.
     * @since 2.0.8
     */
    protected function getTypeCategory(): ?string
    {
        $specialPrimaryKeys = [Schema::TYPE_UUIDPK, Schema::TYPE_BIG_IDENTITY_ALWAYS, Schema::TYPE_BIG_IDENTITY_DEFAULT];
        if (in_array($this->type, $specialPrimaryKeys)) {
            return self::CATEGORY_PK;
        }

        return parent::getTypeCategory();
    }
}