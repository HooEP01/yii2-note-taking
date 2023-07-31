<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\queue\sqs;

use common\base\queue\cli\VerboseBehavior;

/**
 * Class Command
 * @package common\base\queue\sqs
 */
class Command extends \yii\queue\sqs\Command
{
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