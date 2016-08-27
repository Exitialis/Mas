<?php

namespace Exitialis\Mas\Managers;

use Exitialis\Mas\Repositories\UserRepository;
use Exitialis\Mas\User;
use Hautelook\Phpass\PasswordHash;

class AuthManager
{

    /**
     * Репозиторий пользователей.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * AuthManager constructor.
     * @param UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
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

        return $this->checkPassword($user, $password);
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
            default:
                return $hasher->CheckPassword($password, $realPass);
                break;
        }

        return false;
    }
}