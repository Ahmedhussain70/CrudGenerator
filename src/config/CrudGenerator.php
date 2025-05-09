<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Feature
    |--------------------------------------------------------------------------
    |
    | This option controls whether the package's main feature is enabled.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Option
    |--------------------------------------------------------------------------
    |
    | A default setting that your package uses. You can change it as needed.
    |
    */

    'default_option' => 'value',

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    |
    | Example of a configurable API endpoint.
    |
    */

    'api_url' => env('YOUR_PACKAGE_API_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Choose whether to log actions taken by this package.
    |
    */

    'logging' => [
        'enabled' => true,
        'level' => 'info',
    ],

    'providors' => [
        ahmed-hussain70\crud-generator\CrudGeneratorServiceProvider::class,
    ]

];
