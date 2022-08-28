<?php

return [

    /**
     * This is the subdomain where Kaca will be accessible from. If the
     * setting is null, Kaca will reside under the same domain as the
     * application. Otherwise, this value will be used as the subdomain.
     */
    'domain' => env('LARAVEL_UI_DOMAIN'),

    /**
     * This is the URI path where Kaca will be accessible from. Feel free
     * to change this path to anything you like. Note that the URI will not
     * affect the paths of its internal API that aren't exposed to users.
     */
    'prefix' => env('LARAVEL_UI_PREFIX'),
];
