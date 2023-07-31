<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\db\pgsql;

/**
 * Class Schema
 * @package common\base\db\pgsql
 */
class Schema extends \yii\db\pgsql\Schema
{
    const TYPE_NANOID = 'nanoid';
    const TYPE_NANOID_PK = 'nanoid_pk';
    const TYPE_UUID = 'uuid';
    const TYPE_UUIDPK = 'uuidpk';
    const TYPE_TIMESTAMP_TIMEZONE = 'timestamptz';
    const TYPE_TEXT_ARRAY = 'text_array';
    const TYPE_UUID_ARRAY = 'uuid_array';

    const TYPE_TSVECTOR = 'tsvector';

    const TYPE_BIG_IDENTITY_DEFAULT = 'big_identity_default';
    const TYPE_BIG_IDENTITY_ALWAYS = 'big_identity_always';

    /**
     * Creates a query builder for the PostgreSQL database.
     * @return QueryBuilder query builder instance
     */
    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->db);
    }

    /**
     * {@inheritdoc}
     */
    public function createColumnSchemaBuilder($type, $length = null)
    {
        return new ColumnSchemaBuilder($type, $length, $this->db);
    }
}
