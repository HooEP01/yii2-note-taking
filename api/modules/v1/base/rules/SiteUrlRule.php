<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Hustle Hero
 */

namespace api\modules\v1\base\rules;


use api\base\rules\UrlRule;

/**
 * Class SiteUrlRule
 * @package api\modules\v1\base\rules
 */
class SiteUrlRule extends UrlRule
{
    /**
     * @var string
     */
    public $controller = 'v1/site';

    /**
     * @var array
     */
    public $patterns = [
        'GET' => 'index',
        'GET test' => 'test',
    ];
}