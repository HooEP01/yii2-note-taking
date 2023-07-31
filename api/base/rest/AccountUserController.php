<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\rest;

use common\models\Account;
use common\models\AccountUser;
use yii\web\IdentityInterface;
use Yii;

/**
 * Class AccountUserController
 * @property AccountUser $accountUser
 * @property Account $account
 * @package api\base\rest
 */
class AccountUserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['unitAuth'] = [
            'class' => '\api\base\filters\AccountUserAuth',
            'user' => Yii::$app->accountUser,
            'optional' => $this->agentOptionals(),
        ];

        return $behaviors;
    }

    /**
     * @return array
     */
    protected function agentOptionals()
    {
        return $this->optionals();
    }

    /**
     * @return AccountUser|IdentityInterface
     */
    protected function getAccountUser()
    {
        return Yii::$app->accountUser->getIdentity();
    }

    /**
     * @return Account
     */
    protected function getAccount()
    {
        return $this->accountUser ? $this->accountUser->account : null;
    }
}
