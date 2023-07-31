<?php
/**
 * @author RYU Chua <ryu@riipay.my>
 * @link https://riipay.my
 * @copyright Copyright (c) Riipay
 */

namespace common\base\audit\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[AuditEntry]].
 *
 * @see AuditEntry
 */
class AuditEntryQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return AuditEntry[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AuditEntry|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
