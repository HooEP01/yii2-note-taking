<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\web;

use common\base\enum\LanguageCode;
use Yii;

/**
 * Class User
 * @property User $identity
 * @package api\base\web
 */
class User extends \yii\web\User
{
    /**
     * @inheritdoc
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        if ($identity instanceof \common\models\User) {
            $languageCode = LanguageCode::resolveCode(Yii::$app->language);
            if ($identity->languageCode != $languageCode) {
                $identity->languageCode = $languageCode;
                $identity->save(false);
            }
        }

        parent::afterLogin($identity, $cookieBased, $duration);
    }
}
