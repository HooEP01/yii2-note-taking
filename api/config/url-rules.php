<?php

/**
 * @author RYU Chua <me@ryu.my>
 * @link https://ryu.my
 * @copyright Copyright (c) Hustle Hero Sdn. Bhd.
 */
return [
    '/' => 'site/index',
    'OPTIONS <r:.*?>' => 'site/index',
    '/_alert' => 'site/alert',

    //-- Version 1 API
    'GET /v1' => 'v1/site/index',
    'GET /v1/navigation' => 'v1/site/navigation',
    'GET /v1/page/<code>' => 'v1/page/view',
    ['class' => 'api\modules\v1\base\rules\MeUrlRule'],
    ['class' => 'api\modules\v1\base\rules\SiteUrlRule'],
    ['class' => 'api\modules\v1\base\rules\CommonUrlRule'],
    ['class' => 'api\modules\v1\base\rules\FaqUrlRule'],
    ['class' => 'api\modules\v1\base\rules\AuthUrlRule'],
];
