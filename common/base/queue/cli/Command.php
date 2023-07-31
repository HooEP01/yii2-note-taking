<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\queue\cli;

/**
 * Class Command
 * @package common\base\queue\core
 */
class Command extends \yii\queue\cli\Command
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

    /**
     * @inheritdoc
     */
    protected function isWorkerAction($actionID)
    {
        return in_array($actionID, ['run' ,'listen'], true);
    }
}
