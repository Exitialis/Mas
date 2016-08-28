<?php

$factory->define(Exitialis\Mas\User::class, function (Faker\Generator $faker) {

    $config = config('mas.repositories.user');

    return [
        $config['login_column'] => $faker->name,
        $config['password_column'] => $faker->password,
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
