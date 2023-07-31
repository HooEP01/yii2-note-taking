<?php
/**
 * @author RYU Chua <ryu@riipay.my>
 * @link https://riipay.my
 * @copyright Copyright (c) Riipay
 */

namespace common\base\audit\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[AuditTrail]].
 *
 * @see AuditTrail
 */
class AuditTrailQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return AuditTrail[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AuditTrail|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
