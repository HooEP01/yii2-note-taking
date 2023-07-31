<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class FolderStatus
 * @package app\base\enum
 */
class FolderStatus extends BaseEnum
{
    const ONLINE = 'online';
    const LOCAL = 'local';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::ONLINE => Yii::t('enum', 'status.online'),
            self::LOCAL => Yii::t('enum', 'status.local'),
        ];
    }
}
