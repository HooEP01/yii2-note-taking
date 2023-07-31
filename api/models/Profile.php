<?php

namespace api\models;

use api\base\helpers\DateTimeHelper;
use common\base\enum\Gender;
use common\base\helpers\ArrayHelper;
use common\base\helpers\UuidHelper;
use common\models\User;
use common\models\UserQuery;
use common\models\UserSocial;
use Yii;

/**
 * Class Profile
 * @package api\models
 */
class Profile extends User
{
    /**
     * @return array|false
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'firstName';
        $fields[] = 'lastName';
        $fields[] = 'country';
        $fields[] = 'email';
        $fields[] = 'phone';
        $fields['gender'] = function () {
            return ['code' => $this->gender, 'name' => Gender::resolve($this->gender)];
        };

        $fields['social'] = function () {
            return $this->getSocialField();
        };

        return $fields;
    }

    /**
     * @return array
     */
    protected function getSocialField()
    {
        $data = [];

        $facebook = UserSocial::find()->user($this)->active()->facebook()->limit(1)->one();
        if ($facebook instanceof UserSocial) {
            $data['facebook'] = $facebook->toArray();
        }

        $google = UserSocial::find()->user($this)->active()->google()->limit(1)->one();
        if ($google instanceof UserSocial) {
            $data['google'] = $google->toArray();
        }

        return $data;
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }
}
