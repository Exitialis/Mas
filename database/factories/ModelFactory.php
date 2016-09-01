<?php

use Exitialis\Mas\Managers\Hash\HashManager;

$factory->define(Exitialis\Mas\User::class, function (Faker\Generator $faker) {

    $config = config('mas.repositories.user');

    $crypt = new HashManager(config('mas.hash'));

    return [
        $config['login_column'] => $faker->userName,
        $config['password_column'] => $crypt->hash('12345'),
    ];
});

$factory->define(Exitialis\Mas\MasKey::class, function (Faker\Generator $faker) {

    return [
        'username' => function(array $key) {
            return Exitialis\Mas\User::find($key['user_id'])->login;
        },
        'uuid' => function(array $key) {
            return uuidFromString($key['username']);
        },
        'session' => generateStr(),
        'user_hash' => function(array $key) {
            return str_replace('-', '', $key['uuid']);
        }
    ];
});
