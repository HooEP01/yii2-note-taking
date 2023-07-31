<?php

/**
 *@author RYU Chua <ryu@icares.community>
 * @link https://icares.community
 * @copyright Copyright (c) Coresystem Solutions Sdn Bhd
 */

namespace console\controllers;

use common\base\console\Controller;
use common\base\enum\UserRole;
use yii\rbac\ManagerInterface;
use Yii;

/**
 * Class RbacController
 * @package console\controllers
 */
class RbacController extends Controller
{
    /**
     * @var ManagerInterface
     */
    protected $_authManager;

    /**
     * Initialize or Re-initialized Auth Permission
     */
    public function actionInit()
    {
        if ($this->confirm('Continue ?')) {
            $this->info('Purge all current RBAC settings...');
            $auth = $this->getAuthManager();
            $auth->removeAll();

            $this->initializeRoles();
            $this->success('System Roles created !');
        } else {
            $this->warning('By passing --confirm, RBAC will be reset to system default !!!');
        }
    }

    /**
     * Initialize all roles
     */
    protected function initializeRoles()
    {
        $auth = $this->getAuthManager();

        $staff = $auth->createRole(UserRole::STAFF);
        $auth->add($staff);

        $admin = $auth->createRole(UserRole::ADMIN);
        $auth->add($admin);
        $auth->addChild($admin, $staff);

        $superAdmin = $auth->createRole(UserRole::SUPER_ADMIN);
        $auth->add($superAdmin);
        $auth->addChild($superAdmin, $admin);
    }

    /**
     * @return ManagerInterface
     */
    protected function getAuthManager()
    {
        if (isset($this->_authManager)) {
            return $this->_authManager;
        }

        return $this->_authManager = Yii::$app->authManager;
    }
}
