<?php

namespace Exitialis\Mas\Managers;

use Exitialis\Mas\MasKey;
use Exitialis\Mas\Repository\Contracts\KeyRepositoryInterface;
use Exitialis\Mas\Repository\Contracts\RepositoryInterface;
use Exitialis\Mas\Repository\Eloquent\KeyRepository;
use Exitialis\Mas\User;

class KeyManager
{
    /**
     * Ключи пользователей.
     *
     * @var RepositoryInterface
     */
    protected $keys;

    /**
     * KeyManager constructor.
     * @param $keys
     */
    public function __construct(KeyRepositoryInterface $keys)
    {
        $this->keys = $keys;
    }

    /**
     * Сохранить/обновить ключи пользователя в базе.
     *
     * @param User $user
     * @return MasKey
     */
    public function updateOrCreate(User $user)
    {
        $login = $user->login;
        $uuid = uuidFromString($login);

        return $this->keys->updateOrCreate([
            'uuid' => $uuid
        ], [
            'user_id' => $user->getKey(),
            'uuid' => $uuid,
            'user_hash' => str_replace("-", "", $uuid),
            'session' => generateStr(),
            'username' => $login
        ]);
    }

    /**
     * Получить токен пользователя.
     *
     * @param User $user
     * @return mixed
     */
    public function getUserToken(User $user)
    {
        return $this->keys->getUserToken($user);
    }

}