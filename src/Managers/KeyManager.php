<?php

namespace Exitialis\Mas\Managers;

use Exitialis\Mas\MasKey;
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
    public function __construct(RepositoryInterface $keys)
    {
        $this->keys = $keys;
    }

    /**
     * Сохранить/обновить ключи пользователя в базе.
     *
     * @param User $user
     * @return MasKey
     */
    public function save(User $user)
    {
        $login = $user->login;
        $uuid = uuidFromString($login);

        return $this->keys->updateOrCreate([
            'uuid' => $uuid,
            'user_hash' => str_replace("-", "", $uuid),
            'session' => generateStr(),
            'username' => $login
        ]);
    }

}