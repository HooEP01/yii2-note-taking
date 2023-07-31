<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\base\rules;

use api\base\rules\UrlRule;

/**
 * Class MeUrlRule
 * @package api\modules\v1\base\rules
 */
class MeUrlRule extends UrlRule
{
    /**
     * @var string
     */
    public $controller = 'v1/me';

    /**
     * @var array
     */
    public $patterns = [
        'GET profile' => 'profile',
        'PUT profile' => 'profile-update',
        'PUT avatar' => 'avatar',
        'POST bind' => 'bind',
    ];
}