<?php
/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */

namespace api\base\rules;

/**
 * Class UrlRule
 * @package api\base\rules
 */
class UrlRule extends \yii\rest\UrlRule
{
    /**
     * @var bool
     */
    public $pluralize = false;

    /**
     * @var array
     */
    public $tokens = [
        '{id}' => '<id:[2-9a-z-A-Z]{21,23}>',
        '{action}' => '<action:[0-9a-zA-Z\-]+>',
        '{uuid}' => '<uuid:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}>',
    ];

    /**
     * @var array the default configuration for creating each URL rule contained by this rule.
     */
    public $ruleConfig = [
        'class' => 'yii\web\UrlRule',
    ];
}
