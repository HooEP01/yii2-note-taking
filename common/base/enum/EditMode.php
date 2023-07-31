<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

use Yii;

/**
 * Class EditMode
 * @package common\base\enum
 */
class EditMode extends BaseEnum
{
    const PREVIEW = 'preview';
    const EDIT = 'edit';

    /**
     * @return array
     */
    public static function options()
    {
        return [
            self::PREVIEW => Yii::t('enum', 'edit_mode.preview'),
            self::EDIT => Yii::t('enum', 'edit_mode.edit'),
        ];
    }
}