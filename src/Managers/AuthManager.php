<?php

namespace Exitialis\Mas\Managers;

use Exitialis\Mas\Managers\Hash\HashManager;
use Exitialis\Mas\Repository\Contracts\RepositoryInterface;
use Exitialis\Mas\Repository\Contracts\UserRepositoryInterface;
use Exitialis\Mas\User;
use Hautelook\Phpass\PasswordHash;

class AuthManager
{

    /**
     * Репозиторий пользователей.
     *
     * @var UserRepositoryInterface
     */
    protected $users;

    /**
     * Ключи пользователя.
     *
     * @var RepositoryInterface
     */
    protected $keys;

    /**
     * Шифровальщик.
     *
     * @var HashManager
     */
    protected $crypt;

    /**
     * AuthManager constructor.
     *
     * @param UserRepositoryInterface $users
     * @param KeyManager $keys
     */
    public function __construct(UserRepositoryInterface $users, KeyManager $keys, HashManager $crypt)
    {
        $this->users = $users;
        $this->keys = $keys;
        $this->crypt = $crypt;
    }

    /**
     * Авторизовать пользователя по его данным.
     *
     * @param $login
     * @param $password
     * @return bool
     */
    public function login($login, $password)
    {
        if ( ! $user = $this->users->findByLogin($login)) {
            return false;
        }

        if ($this->checkPassword($user, $password)) {
            return $this->keys->updateOrCreate($user);
        }

        return false;
    }

    /**
     * Проверить пароль на правильность.
     *
     * @param User $user
     * @param $password
     * @return bool
     */
    public function checkPassword(User $user, $password)
    {
        return $this->crypt->checkValue($password, $user->password);
    }

}