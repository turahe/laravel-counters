<?php

return [
    'models' => [
        'counter' => Turahe\Counters\Models\Counter::class,
    ],

    'tables' => [
        /*
     * This is the name of the table that will be created by the migration and
     * used by the counter model shipped with this package.
     */
        'table_name' => 'counters',
        'table_pivot_name' => 'counterables',
    ],
    /*
    * This is the database connection that will be used by the migration and
     * the counter model shipped with this package. In case it's not set
     * Laravel's database.default will be used instead.
     * */
    'database_connection' => env('COUNTER_DB_CONNECTION'),
];
