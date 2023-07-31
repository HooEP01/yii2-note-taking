<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace console\controllers;

use common\base\console\Controller;
use common\base\enum\CurrencyCode;
use common\base\enum\Gender;
use common\base\enum\NameFormat;
use common\base\enum\SocialChannel;
use common\base\enum\UserRole;
use common\base\helpers\ArrayHelper;
use common\models\User;
use common\models\UserEmail;
use common\models\UserPhone;
use common\models\UserSocial;
use yii\base\Exception;
use Yii;

/**
 * Class UserController
 * @package console\controllers
 */
class UserController extends Controller
{
    /**
     * show the user current MFA OTP
     * @param $id
     * @throws Exception
     */
    public function actionMfa($id)
    {
        if ((int) $id === 1) {
            $id = '88888888-8888-8888-8888-888888888888';
        }

        /** @var User $user */
        $user = User::findOne($id);
        if ($user === null) {
            throw new Exception('User Not Found, ID: ' . $id);
        }

        $this->cyan('User: ' . $user->getDisplayName());
        $this->purple('Current MFA: ' . $user->getTimeBaseOTP()->now());
    }

    /**
     * reset user password
     * @param int $id
     * @param null|string $password
     * @throws Exception
     * @throws \Exception
     */
    public function actionResetPassword($id, $password = null)
    {
        if ((string) $id === '1') {
            $id = '88888888-8888-8888-8888-888888888888';
        }

        /** @var User $user */
        $user = User::findOne($id);
        if ($user === null) {
            throw new Exception('User Not Found, ID: ' . $id);
        }

        if ($password === null) {
            $password = $this->prompt('Please enter the password :');
        }

        if ($this->confirm(sprintf('Reset %s password to "%s" ?', $user->username, $password))) {
            $user->setPassword($password);
            if ($user->save(false)) {
                $this->success(sprintf('Password of User "%s" is now: %s', $user->username, $password));
            }
        }
    }

    /**
     * create the first super admin user
     * @return mixed
     * @throws Exception
     */
    public function actionCreateSuperAdmin()
    {
        $count = User::find()->count();
        if ($count >= 1) {
            $this->error('Already have users ! please use admin portal to create new user !');
            return false;
        }

        $ids = ArrayHelper::getValue(Yii::$app->params, 'system.admin.ids', ['88888888-8888-8888-8888-888888888888']);
        if (empty($ids)) {
            throw new Exception('"system.admin.ids" not set !');
        }

        $valid = true;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $password = $this->prompt('Please enter new password: ');

            $data = [
                'id' => current($ids),
                'dateOfBirth' => '1986-10-25',
                'username' => 'ryu',
                'gender' => Gender::MALE,
                'displayName' => 'RYU Chua',
                'firstName' => 'RYU',
                'lastName' => 'Chua',
                'fullName' => 'Ryu Chua Boon Yew',
                'nameFormat' => NameFormat::FIRST_LAST,
                'roleItems' => [UserRole::SUPER_ADMIN],
                'referrerCode' => 'RYUCHUA',
                'currencyCode' => CurrencyCode::AUSTRALIAN_DOLLAR,
                'countryCode' => 'MY',
                'stateCode' => 'MY-01',
                'postcode' => '81100',
            ];
            $user = new User($data);
            $user->setPassword($password);
            $valid = $valid && $user->save();

            if ($valid) {
                $data = ['userId' => $user->id, 'address' => 'ryu@alpstein.my', 'isVerified' => true];
                $email = new UserEmail($data);
                $valid = $valid && $email->save();
                if (!$valid && $email->hasErrors()) {
                    $this->error($email->errors);
                }

                $data = ['userId' => $user->id, 'prefix' => '+60', 'number' => '167900392', 'isVerified' => true];
                $phone = new UserPhone($data);
                $valid = $valid && $phone->save();
                if (!$valid && $phone->hasErrors()) {
                    $this->error($phone->errors);
                }

                $data = [
                    'userId' => $user->id,
                    'channel' => SocialChannel::FACEBOOK,
                    'channelId' => '10156149625618076',
                    'channelName' => 'Ryu Chua Boon Yew',
                    'isVerified' => true,
                ];
                $facebook = new UserSocial($data);
                $valid = $valid && $facebook->save();
                if (!$valid && $facebook->hasErrors()) {
                    $this->error($facebook->errors);
                }
            } elseif ($user->hasErrors()) {
                $this->error($user->errors);
            }

            $valid ? $transaction->commit() : $transaction->rollBack();
        } catch (\Exception $e) {
            $valid = false;
            $transaction->rollBack();
            $this->error($e->getMessage());
        }

        if ($valid) {
            $this->success('User RYU Chua created !');
        }
    }
}