<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[PageContent]].
 *
 * @see PageContent
 */
class PageContentQuery extends ActiveQuery
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
     * @return $this
     */
    public function orderByDefault()
    {
        return $this->orderBy([
            $this->getColumnName('position') => SORT_ASC,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return PageContent[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PageContent|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
