<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;
use common\base\enum\FaqType;

/**
 * This is the ActiveQuery class for [[Faq]].
 *
 * @see Faq
 */
class FaqQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function general()
    {
        return $this->type(FaqType::GENERAL);
    }

    /**
     * @param $value
     * @return $this
     */
    public function type($value)
    {
        return $this->andWhere([$this->getColumnName('type') => $value]);
    }

    /**
     * @return $this
     */
    public function orderByDefault()
    {
        return $this->orderBy([
            $this->getColumnName('position') => SORT_ASC,
            $this->getColumnName('id') => SORT_ASC,
        ]);
    }

    /**
     * {@inheritdoc}
     * @return Faq[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Faq|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
