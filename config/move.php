<?php

return [

    'layout' => 'move::layouts.app',

    'auth' => [
        'enabled' => true,

        'middlewares' => ['auth', 'verified'],
    ],

    'middlewares' => [
        // Add your custom middlewares here
        // You are allowed to over
        'web',
    ],

    'load_resource_routes' => true,

    'use_resource_seeders' => env('MOVE_USE_RESOURCE_SEEDERS', false),
];
