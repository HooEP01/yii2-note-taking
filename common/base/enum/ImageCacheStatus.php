<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class ImageCacheStatus
 * @package common\base\enum
 */
class ImageCacheStatus extends BaseEnum
{
    const PENDING ='pending';
    const READY = 'ready';
    const PURGING = 'purging';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::PENDING => Yii::t('enum', 'image_cache_status.pending'),
            self::READY => Yii::t('enum', 'image_cache_status.ready'),
            self::PURGING => Yii::t('enum', 'image_cache_status.purging'),
        ];
    }
}