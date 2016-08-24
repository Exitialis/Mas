<?php

namespace Exitialis\Mas\Managers;

use Exitialis\Mas\User;
use Hautelook\Phpass\PasswordHash;
use Illuminate\Database\Eloquent\Model;

class AuthManager
{

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
                $bool = $realPass == md5(md5($password));
                break;
            default:
                return $hasher->CheckPassword($password, $realPass);
                break;
        }

        return false;
    }
}