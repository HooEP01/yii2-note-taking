<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\queue\db;

use common\base\queue\cli\VerboseBehavior;

/**
 * Class Command
 * @package common\base\queue\db
 */
class Command extends \yii\queue\db\Command
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'info' => InfoAction::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $valid = parent::beforeAction($action);

        if ($this->canVerbose($action->id) && $this->verbose) {
            $this->queue->attachBehavior('verbose', [
                'class' => VerboseBehavior::class,
                'command' => $this,
            ]);
        }

        return $valid;
    }
}