<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://propertygenie.my
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\audit;

use common\base\audit\models\AuditEntry;
use yii\base\Application;
use yii\base\BaseObject;
use yii\web\Application as WebApplication;
use Yii;

/**
 * Class Audit
 * @property AuditEntry $entry
 * @property string $userId
 * @package common\base\audit
 */
class Audit extends BaseObject
{
    /**
     * @var AuditEntry
     */
    private $_entry;

    /**
     * initialize and register action
     */
    public function init()
    {
        parent::init();

        $app = Yii::$app;
        //$app->on(Application::EVENT_BEFORE_ACTION, [$this, 'onBeforeAction']);
        $app->on(Application::EVENT_AFTER_REQUEST, [$this, 'onAfterRequest']);
    }

    /**
     * finalizing the entry
     */
    public function onAfterRequest()
    {
        if (isset($this->_entry) && $this->_entry instanceof AuditEntry) {
            $this->_entry->finalize();
        }
    }

    /**
     * @return AuditEntry
     */
    public function getEntry()
    {
        if (isset($this->_entry)) {
            return $this->_entry;
        }

        return $this->_entry = AuditEntry::create();
    }

    /**
     * @return string|null
     */
    public function getUserId()
    {
        return (Yii::$app instanceof WebApplication && Yii::$app->user) ? Yii::$app->user->id : null;
    }
}
