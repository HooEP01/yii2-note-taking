<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\base\web;

use backend\base\rbac\AuthManager;
use common\base\enum\UserRole;
use common\models\User as UserModel;
use Yii;

/**
 * Class User
 * @property array $roles
 * @package backend\base\web
 */
class User extends \common\base\web\User
{
    /**
     * @var array
     */
    private $_roles;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        //Dynamic assign roles on runtime
        if (!$this->getIsGuest() && !empty($this->roles) && ($auth = Yii::$app->authManager) instanceof AuthManager) {
            foreach ($this->roles as $role) {
                if (($item = $auth->getRole($role)) !== null) {
                    $auth->assign($item, $this->getId());
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getRoles()
    {
        if (isset($this->_roles) && is_array($this->_roles)) {
            return $this->_roles;
        }

        if ($this->getIsGuest()) {
            return $this->_roles = [];
        }

        if ($this->identity instanceof UserModel) {
            $this->_roles = $this->identity->roleItems;
            return $this->_roles = array_unique($this->_roles);
        }

        return $this->_roles = [];
    }

    /**
     * @param string $permissionName
     * @param array $params
     * @param bool $allowCaching
     * @return bool
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        //Technically, super admin can do anything !
        if (parent::can(UserRole::SUPER_ADMIN)) {
            return true;
        }

        return parent::can($permissionName, $params, $allowCaching);
    }
}
