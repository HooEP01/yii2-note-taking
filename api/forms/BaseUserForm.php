<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\forms;


use common\models\User;
use yii\base\Model;

/**
 * Class BaseUserForm
 * @property User $user
 * @package api\forms
 */
class BaseUserForm extends Model
{
    /**
     * @var User
     */
    private $_user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param User $value
     */
    protected function setUser(User $value)
    {
        $this->_user = $value;
    }
}