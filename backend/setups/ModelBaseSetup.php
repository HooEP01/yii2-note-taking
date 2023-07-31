<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\setups;


use yii\db\ActiveRecord;

/**
 * Class ModelBase
 * @package backend\setups
 */
abstract class ModelBaseSetup extends BaseSetup
{
    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var ActiveRecord
     */
    private $_model;

    /**
     * @return ActiveRecord
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param ActiveRecord $model
     */
    public function setModel(ActiveRecord $model)
    {
        $this->_model = $model;
    }
}