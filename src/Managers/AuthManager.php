<?php

namespace Exitialis\Mas\Managers;

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
     * AuthManager constructor.
     *
     * @param UserRepositoryInterface $users
     * @param KeyManager $keys
     */
    public function __construct(UserRepositoryInterface $users, KeyManager $keys)
    {
        $this->users = $users;
        $this->keys = $keys;
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
        $hash = config('mas.hash');

        $realPass = $user->password;

        $hasher = new PasswordHash(8, false);

         switch ($hash) {
            case 'wp':
                return $hasher->CheckPassword($password, $realPass);
                break;
            case 'dle':
                return $realPass === md5(md5($password));
                break;
        }

        return false;
    }

}