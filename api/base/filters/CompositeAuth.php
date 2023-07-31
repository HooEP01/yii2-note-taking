<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */


namespace api\base\filters;

use yii\web\UnauthorizedHttpException;

/**
 * Class CompositeAuth
 * @package api\base\filters
 */
class CompositeAuth extends \yii\filters\auth\CompositeAuth
{
    /**
     * @inheritdoc
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Invalid Token');
    }
}
