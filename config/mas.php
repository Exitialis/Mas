<?php

return [

    'users' => [
        'model' => App\User::class,
    ],

    'route_prefix' => 'mas',

    /**
     * Available hashes:
     * wp, dle
     */
    'hash' => 'wp',

    'repositories' => [
        'user' => [
            'login_column' => 'user_login',
            'password_column' => 'user_pass',
            'table_name' => 'bjsvyp8zhw_users',
            'key' => 'ID',
        ],
    ],

    'textures' => [
        'path' => [
            'skin' => 'textures/skin',
            'cloak' => 'textures/cloak'
        ],
        'skin_default' => 'default'
    ],

    'path' => [
        'clients' =>  "clients"
    ],

];