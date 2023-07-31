<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Document]].
 *
 * @see Document
 */
class DocumentQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Document[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Document|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
