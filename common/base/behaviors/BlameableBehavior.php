<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\behaviors;

use Yii;

/**
 * Class BlameableBehavior
 * @package common\base\behaviors
 */
class BlameableBehavior extends \yii\behaviors\BlameableBehavior
{
    public $createdByAttribute = 'createdBy';
    public $updatedByAttribute = 'updatedBy';

    /**
     * @inheritdoc
     * @param \yii\base\Event $event
     */
    protected function getValue($event)
    {
        if (YII_CONSOLE_MODE) {
            return '11111111-1111-1111-1111-111111111111'; //console mode
        }

        if (Yii::$app->user->isGuest) {
            return '00000000-0000-0000-0000-000000000000';
        }
        
        return parent::getValue($event);
    }
}
