<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Language]].
 *
 * @see Language
 */
class LanguageQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function orderByDefault()
    {
        return $this->orderBy([
            $this->getColumnName('position') => SORT_ASC
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Language[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Language|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
