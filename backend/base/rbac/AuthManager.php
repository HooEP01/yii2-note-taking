<?php
/**
 * @author RYU Chua <ryu@icares.community>
 * @link https://icares.community
 * @copyright Copyright (c) Coresystem Solutions Sdn Bhd
 */

namespace backend\base\rbac;

use yii\rbac\Assignment;
use yii\rbac\PhpManager;

/**
 * Class AuthManager
 * @package backend\base\rbac
 */
class AuthManager extends PhpManager
{
    /**
     * dynamic assign role on runtime after user init
     * @inheritDoc
     */
    public function assign($role, $userId)
    {
        $this->assignments[$userId][$role->name] = new Assignment([
            'userId' => $userId,
            'roleName' => $role->name,
            'createdAt' => time(),
        ]);

        return $this->assignments[$userId][$role->name];
    }
}