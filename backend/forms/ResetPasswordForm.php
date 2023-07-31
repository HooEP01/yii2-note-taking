<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\forms;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Class ResetPasswordForm
 * @property User $user
 * @package backend\forms
 */
class ResetPasswordForm extends Model
{
    /**
     * @var string
     */
    public $new_password;
    /**
     * @var string
     */
    public $confirm_password;

    /**
     * @var User
     */
    private $_model;

    /**
     * @var bool
     */
    private $_allowed = true;

    /**
     * initialize
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        if ($this->getUser() instanceof User && $this->getUser()->getIsSuperAdmin()) {
            $this->_allowed = false;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['new_password', 'confirm_password'], 'required'],
            ['new_password', 'string', 'min' => 8],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'new_password' => Yii::t('form', 'password.new_password'),
            'confirm_password' => Yii::t('form', 'password.confirm_password')
        ];
    }

    /**
     * @return bool
     */
    public function getIsAllowed()
    {
        return (bool) $this->_allowed;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_model;
    }

    /**
     * @param User $value
     */
    protected function setUser(User $value)
    {
        $this->_model = $value;
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     * @throws \yii\base\Exception
     */
    public function process()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->setPassword($this->new_password);
            return $user->save(false);
        }

        return false;
    }
}