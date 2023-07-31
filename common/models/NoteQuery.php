<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Note]].
 *
 * @see Note
 */
class NoteQuery extends ActiveQuery
{

    /**
     * @param Folder|string|array $value
     * @return $this
     */
    public function folder($value)
    {
        if ($value instanceof Folder) {
            $value = $value->id;
        }

        return $this->andWhere([$this->getColumnName('folder_id') => $value]);
    }

    /**
     * {@inheritdoc}
     * @return Note[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Note|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
