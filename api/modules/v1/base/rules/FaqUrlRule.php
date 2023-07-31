<?php
/**
 * @author RYU Chua <ryu@alpstein.my>
 * @link https://www.hustlehero.com.au
 * @copyright Copyright (c) Property Genie
 */

namespace api\modules\v1\base\rules;


use api\base\rules\UrlRule;

/**
 * Class FaqUrlRule
 * @package api\modules\v1\base\rules
 */
class FaqUrlRule extends UrlRule
{
    /**
     * @var string
     */
    public $controller = 'v1/faq';

    /**
     * @var array
     */
    public $patterns = [
        'GET general' => 'general',
    ];
}