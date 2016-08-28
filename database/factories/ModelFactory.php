<?php

use Exitialis\Mas\Managers\Hash\HashManager;

$factory->define(Exitialis\Mas\User::class, function (Faker\Generator $faker) {

    $config = config('mas.repositories.user');

    $crypt = new HashManager(config('mas.hash'));

    return [
        $config['login_column'] => $faker->name,
        $config['password_column'] => $crypt->hash('12345'),
    ];
});

$factory->define(Exitialis\Mas\MasKey::class, function (Faker\Generator $faker) {

    $username = $faker->name;
    $uuid = uuidFromString($username);

    return [
        'username' => $username,
        'uuid' => $uuid,
        'session' => generateStr(),
        'user_hash' => str_replace('-', '', $uuid)
    ];
});
