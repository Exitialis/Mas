<?php

return [

    'users' => [
        'model' => App\User::class,
    ],

    'route_prefix' => 'lk/mas',
    'hash' => 'wp',
    'user' => [
        'login_column' => 'user_login',
        'password_column' => 'user_pass',
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