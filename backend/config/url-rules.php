<?php

return [
    '<_lang:(en|zh|ms)>/' => 'site/index',
    '/' => 'site/index',
    [
        'pattern' => '<_lang:(en|zh|ms)>/<controller:[\w-]+>/<action:[\w-]+>',
        'route' => '<controller>/<action>',
        'defaults' => [
            '_lang' => 'en',
        ],
    ],
    [
        'pattern' => '<_lang:(en|zh|ms)>/<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>',
        'route' => '<module>/<controller>/<action>',
        'defaults' => [
            '_lang' => 'en',
        ],
    ],
];
