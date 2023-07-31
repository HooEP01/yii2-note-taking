<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class PostType
 * @package common\base\enum
 */
class PostType extends BaseEnum
{
    const NEWS = 'news';
    const GUIDES = 'guides';

    const HOME_BANNER = 'home-banner';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::NEWS => \Yii::t('enum', 'post.type.news'),
            self::GUIDES => \Yii::t('enum', 'post.type.guides'),

            self::HOME_BANNER => \Yii::t('enum', 'post.type.banner'),
        ];
    }
}