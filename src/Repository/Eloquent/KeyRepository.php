<?php

namespace Exitialis\Mas\Repository\Eloquent;

use Exitialis\Mas\MasKey;
use Exitialis\Mas\User;

class KeyRepository extends BaseRepository
{
    /**
     * Получить ключи пользователя или создать новые.
     *
     * @param User $user
     * @return MasKey
     */
    public function findOrCreateByUser(User $user)
    {
        if ( ! $keys =  $this->model->where('user_id', $user->getKey())->first()) {
            $keys = new MasKey();
            $keys->user_id = $user->getKey();
        }

        return $keys;
    }

    /**
     * Сохранить/обновить ключи пользователя в базе.
     *
     * @param MasKey $key
     * @param User $user
     * @return MasKey
     */
    public function save(MasKey $key, User $user)
    {
        $login = $user->login;
        
        $uuid = uuidFromString($login);
        $key->uuid = $uuid;
        $key->user_hash = str_replace("-", "", $uuid);
        $key->session = generateStr();
        $key->username = $login;
        $key->save();

        return $key;
    }
}