<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace backend\forms;

use common\models\User;
use yii\base\Model;

/**
 * Class ChangePasswordForm
 * @package common\models
 */
class ChangePasswordForm extends Model
{
    /**
     * @var string
     */
    public $current_password;
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['current_password', 'new_password', 'confirm_password'], 'required'],
            ['new_password', 'string', 'min' => 8],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password'],
            ['current_password', 'validatePassword'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'current_password' => 'Current Password',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm Password'
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->current_password)) {
                $this->addError($attribute, 'Incorrect current password.');
            }
        }
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
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
