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

    $user = factory(Exitialis\Mas\User::class)->create();
    $userName = $user->login;
    $uuid = uuidFromString($userName);

    return [
        'user_id' => $user->getKey(),
        'username' => $userName,
        'uuid' => $uuid,
        'session' => generateStr(),
        'user_hash' => str_replace('-', '', $uuid),
    ];
});
