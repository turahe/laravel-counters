<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Counter Models
    |--------------------------------------------------------------------------
    |
    | Here you can specify the model classes used by the counters package.
    |
    */
    'models' => [
        'counter' => \Turahe\Counters\Models\Counter::class,
        'counterable' => \Turahe\Counters\Models\Counterable::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Counter Tables
    |--------------------------------------------------------------------------
    |
    | Here you can specify the table names used by the counters package.
    |
    */
    'tables' => [
        'table_name' => env('COUNTER_TABLE_NAME', 'counters'),
        'table_pivot_name' => env('COUNTER_PIVOT_TABLE_NAME', 'counterables'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | This is the database connection that will be used by the migration and
    | the counter model shipped with this package. In case it's not set
    | Laravel's database.default will be used instead.
    |
    */
    'database_connection' => env('COUNTER_DB_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the caching options for counters.
    |
    */
    'cache' => [
        'enabled' => env('COUNTER_CACHE_ENABLED', true),
        'prefix' => env('COUNTER_CACHE_PREFIX', 'counters:'),
        'ttl' => env('COUNTER_CACHE_TTL', 3600), // 1 hour in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the cookie options for counters.
    |
    */
    'cookies' => [
        'prefix' => env('COUNTER_COOKIE_PREFIX', 'counters-cookie-'),
        'lifetime' => env('COUNTER_COOKIE_LIFETIME', 60 * 24 * 365), // 1 year in minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default values for counters.
    |
    */
    'defaults' => [
        'initial_value' => env('COUNTER_DEFAULT_INITIAL_VALUE', 0),
        'step' => env('COUNTER_DEFAULT_STEP', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Here you can configure performance-related settings.
    |
    */
    'performance' => [
        'bulk_operations' => env('COUNTER_BULK_OPERATIONS', true),
        'max_bulk_size' => env('COUNTER_MAX_BULK_SIZE', 100),
        'query_timeout' => env('COUNTER_QUERY_TIMEOUT', 30), // seconds
    ],
];
