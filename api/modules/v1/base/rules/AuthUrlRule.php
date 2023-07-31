<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\base\rules;


use api\base\rules\UrlRule;

/**
 * Class AuthUrlRule
 * @package api\modules\v1\base\rules
 */
class AuthUrlRule extends UrlRule
{
    /**
     * @var string
     */
    public $controller = 'v1/auth';

    /**
     * @var array
     */
    public $patterns = [
        'POST firebase' => 'firebase',
        'POST verify' => 'verify',
    ];
}