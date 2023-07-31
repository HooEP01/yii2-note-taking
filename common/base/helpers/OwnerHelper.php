<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

use common\base\db\ActiveRecord;
use common\models\Listing;
use yii\base\BaseObject;

/**
 * Class OwnerHelper
 * @package common\base\helpers
 */
class OwnerHelper extends BaseObject
{
    /**
     * @param string $type
     * @param string $key
     * @return ActiveRecord|Listing
     */
    public static function resolve($type, $key)
    {
        if ($type === Listing::tableName()) {
            return Listing::findOne($key);
        }

        return null;
    }
}
