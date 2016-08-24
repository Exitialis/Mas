<?php

namespace Exitialis\Mas\Managers;

use Illuminate\Database\Eloquent\Model;

class AuthManager
{

    /**
     * Проверить пароль на правильность.
     *
     * @param Model $user
     * @param $password
     * @return bool
     */
    public function checkPassword(Model $user, $password)
    {
        $password_column = config('mas.repositories.user.password_column');
        $hash = config('mas.hash');

        $realPass = $user->$password_column;

         switch ($hash) {
            case 'wp':
                $bool = $realPass == hash_password($password, $realPass);
                break;
            case 'dle':
                $bool = $realPass == md5(md5($password));
                break;
            default:
                $bool = $realPass == hash_password($password, $realPass);
                break;
        }

        return $bool;
    }
}