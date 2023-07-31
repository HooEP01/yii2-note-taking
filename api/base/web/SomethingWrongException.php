<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\base\web;

use yii\web\HttpException;

/**
 * Class SomethingWrongException
 * @package api\base\web
 */
class SomethingWrongException extends HttpException
{
    /**
     * SystemMaintenanceException constructor.
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(501, $message, $code, $previous);
    }
}