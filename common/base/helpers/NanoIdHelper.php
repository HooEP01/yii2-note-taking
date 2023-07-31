<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\helpers;

use Hidehalo\Nanoid\Client;
use yii\base\BaseObject;

/**
 * Class UuidHelper
 * @property $alphabets
 * @package common\base\helpers
 */
class NanoIdHelper extends BaseObject
{
    /**
     * Generate new NanoID
     */
    public static function generate($size = 21)
    {
        $client = new Client();
        return $client->generateId($size);
    }
}