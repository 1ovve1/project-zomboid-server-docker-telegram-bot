<?php

use PZBot\Database\ServerStatus;

return [
    // Bot commands paths
    'commands'     => [
        'paths'   => [
            __DIR__ . '/CustomCommands',
        ],
    ],

    'creator' => [
        'link' => 'https://t.me/b3nchm4d3',
    ],

    // Admins ids (for notification)
    'admins'       => [
        
    ],

    // up and down load paths
    'paths'        => [
        'download' => __DIR__ . '/Download',
        'upload'   => __DIR__ . '/Upload',
    ],

    // limiter mod
    'limiter'      => [
        'enabled' => true,
    ],

    'tables' => [
        ServerStatus::class,
    ]
];
