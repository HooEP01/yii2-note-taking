<?php

/**
 * @author RYU Chua <ryu@riipay.my>
 * @link https://riipay.my
 * @copyright Copyright (c) Riipay
 */

namespace common\base\behaviors;

use common\base\enum\ActionType;
use common\base\helpers\Json;
use common\base\services\Platform;
use common\models\ModelAction;
use yii\base\Behavior;
use yii\di\Instance;
use yii\web\Application as WebApplication;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class ConfigBehavior
 *  @property ActiveRecord $owner
 * @package common\base\behaviors
 */
class ActionBehavior extends Behavior
{
    /**
     * @var Platform|string|array
     */
    public $platform = 'platform';

    public function init()
    {
        parent::init();
        $this->platform = Instance::ensure($this->platform, Platform::class);
    }

    /**
     * @return bool
     */
    public function trackView()
    {
        return $this->createAction(ActionType::VIEW);
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function createAction(string $type)
    {
        if (!ActionType::isValid($type)) {
            return false;
        }

        $userId = (Yii::$app instanceof WebApplication && Yii::$app->user) ? Yii::$app->user->id : null;
        $platformData = [
            'playerId' => (string) $this->platform->getPlayerId(),
            'ipAddress' => (string) $this->platform->getIpAddress(),
            'userAgent' => (string) $this->platform->getUserAgent(),
            'deviceType' => (string) $this->platform->getDeviceType(),
            'deviceName' => (string) $this->platform->getDeviceName(),
            'deviceModel' => (string) $this->platform->getDeviceModel(),
            'browserName' => (string) $this->platform->getBrowserName(),
        ];
        $guestHash = hash('sha256', Json::encode($platformData));
        $timestamp = date('Y-m-d H:i');
        $ownerKey = implode(',', $this->owner->getPrimaryKey(true));

        $identifier = isset($userId) ? hash('sha256', $timestamp . $userId . $type . $ownerKey)
            : hash('sha256', $timestamp . $guestHash . $type . $ownerKey);

        $m = $this->getModelActionByIdentifier($identifier);
        if ($m !== null) {
            return false;
        }

        $m = ModelAction::factory($this->owner);
        if (isset($userId)) {
            $m->userId = $userId;
        } else {
            $m->guestHash = $guestHash;
        }
        $m->type = $type;
        $m->platformData = $platformData;
        $m->identifier = $identifier;

        if ($m->save()) {
            return true;
        } elseif ($m->hasErrors()) {
            Yii::debug($m->errors);
        }

        return false;
    }

    public function countUniqueView()
    {
        return ModelAction::find()->alias('t')
            ->select(["t.guestHash", "t.userId"])
            ->owner($this->owner)
            ->groupBy(["t.guestHash", "t.userId"])
            ->count();
    }

    public function countTotalView(): int
    {
        return ModelAction::find()->alias('t')
            ->owner($this->owner)
            ->count();
    }

    public function countGuestView(): int
    {
        return ModelAction::find()->alias('t')
            ->select("t.guestHash")
            ->owner($this->owner)
            ->userId(null)
            ->groupBy("t.guestHash")
            ->count();
    }

    /**
     * @return array|ModelAction|ActiveRecord|null
     */
    protected function getModelActionByIdentifier($value)
    {
        return ModelAction::find()->alias('t')
            ->identifier($value)
            ->limit(1)
            ->one();
    }
}
