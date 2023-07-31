<?php

return [
    'staff' => [
        'type' => 1,
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'staff',
        ],
    ],
    'superAdmin' => [
        'type' => 1,
        'children' => [
            'admin',
        ],
    ],
];
