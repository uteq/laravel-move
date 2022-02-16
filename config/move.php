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

    /**
     * Whenever a resource is not found it will by default throw an exception.
     * In some cases this is undesirable. Enabling soft fail will
     * instead trigger an abort(404);
     */
    'soft_resource_not_found' => false,
];
