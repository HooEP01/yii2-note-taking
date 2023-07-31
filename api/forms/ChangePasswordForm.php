<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\forms;

use Yii;

/**
 * Class ChangePasswordForm
 * @package api\models
 */
class ChangePasswordForm extends BaseUserForm
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['new_password', 'confirm_password'], 'required'],
            [['current_password'], 'required', 'when' => function () {
                return !$this->getIsPasswordReset();
            }],
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
            'current_password' => Yii::t('form', 'password.change.current_password'),
            'new_password' => Yii::t('form', 'password.change.new_password'),
            'confirm_password' => Yii::t('form', 'password.change.confirm_password'),
        ];
    }

    /**
     * @return bool
     */
    public function getIsPasswordReset()
    {
        return $this->user->getIsPasswordReset();
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            //-- if not reset case
            if (!$this->getIsPasswordReset()) {
                //-- old password must be match
                if (!$user || !$user->validatePassword($this->current_password)) {
                    $this->addError($attribute, Yii::t('form', 'password.change.error.incorrect_current_password'));
                }
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
