<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\services;

use Kreait\Firebase\Factory;
use yii\base\BaseObject;

/**
 * Class Firebase
 * @package common\base\services
 */
class Firebase extends BaseObject
{
    /**
     * example:
     * [
     *      'project_id' => '<project-id>',
     *      'client_id' => '<client-id>',
     *      'client_email' => '<client-email>',
     *      'private_key' => '<private-key>',
     * ]
     * @var array
     */
    public $credential = [];
    /**
     * @var string
     */
    public $databaseUri;

    /**
     * @var Factory
     */
    private $_factory;

    /**
     * @return Factory
     */
    public function getFactory()
    {
        if (isset($this->_factory)) {
            return $this->_factory;
        }

        $factory = (new Factory)->withServiceAccount($this->credential);
        if (isset($this->databaseUri)) {
            $factory = (new Factory)->withServiceAccount($this->credential)->withDatabaseUri($this->databaseUri);
        }

        return $this->_factory = $factory;
    }

    /**
     * @return \Kreait\Firebase\Database
     */
    public function getDatabase()
    {
        return $this->getFactory()->createDatabase();
    }

    /**
     * @return \Kreait\Firebase\Auth
     */
    public function getAuth()
    {
        return $this->getFactory()->createAuth();
    }
}
