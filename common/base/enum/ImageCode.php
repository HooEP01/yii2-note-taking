<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class ImageCode
 * @package common\base\enum
 */
class ImageCode extends BaseEnum
{
    const HOME_POPUP = 'home-popup';
    const BANNER = 'banner';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::HOME_POPUP => Yii::t('enum', 'image_code.home-popup'),
            self::BANNER => Yii::t('enum', 'image_code.banner'),
        ];
    }
}
