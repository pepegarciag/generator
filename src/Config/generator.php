<?php

return [

    /*
   |--------------------------------------------------------------------------
   | Models config
   |--------------------------------------------------------------------------
   |
   | Here you can specify default models settings.
   |
   */
    'models' => [
        'namespace'  => 'App\Models',
        'path'       => app_path('Models/'),
        'extends'    => 'Illuminate\Database\Eloquent\Model',
        'timestamps' => [
            'enabled' => true,
            'fields' => [
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'deleted_at' => 'deleted_at',
            ],
        ],
        'soft_delete' => true,
        'primary_key' => 'id',
    ],

    /*
   |--------------------------------------------------------------------------
   | Controllers config
   |--------------------------------------------------------------------------
   |
   | Here you can specify  controllers settings
   |
   */
    'controllers' => [
        'namespace' => 'App\Http\Controllers',
        'path'      => app_path('Http/Controllers/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API config
    |--------------------------------------------------------------------------
    |
    | Here you can specify  API settings
    |
    */
    'api' => [
        'routes' => base_path('routes/api.php'),
        'route_prefix' => 'api',
        'version' => 'v1',
        'controllers' => [
            'namespace' => 'App\Http\Controllers\API',
            'path'      => app_path('Http/Controllers/API/'),
        ],
        'request' => [
            'namespace' => 'App\Http\Requests\API',
            'path' => app_path('Http/Requests/API/'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migrations config
    |--------------------------------------------------------------------------
    |
    | Here you can specify  migrations settings
    |
    */
    'migrations' => [
        'path' => base_path('database/migrations/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Views config
    |--------------------------------------------------------------------------
    |
    | Here you can specify  migrations settings
    |
    */
    'views' => [
        'path' => base_path('resources/views/'),
    ],

];
