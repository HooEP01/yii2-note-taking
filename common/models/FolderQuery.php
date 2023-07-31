<?php
/**
* @copyright Copyright (c) Hustle Hero
*/

namespace common\models;

use common\base\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Folder]].
 *
 * @see Folder
 */
class FolderQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Folder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Folder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
