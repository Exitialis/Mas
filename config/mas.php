<?php

return [

    'users' => [
        'model' => App\User::class,
    ],

    'route_prefix' => 'lk/mas',

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

    'path' => [
        'uploaddirs' => "skins/skin",
        'uploaddirc' => "skins/cape",
        'clients' =>  "clients"
    ],

    'url' => [
        'skin' => "lk/skins/skin",
        'cape' => "lk/skins/cape"
    ]

];