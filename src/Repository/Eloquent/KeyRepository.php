<?php

namespace Exitialis\Mas\Repository\Eloquent;

use Exitialis\Mas\MasKey;
use Exitialis\Mas\Repository\Contracts\RepositoryInterface;
use Exitialis\Mas\Repository\Contracts\KeyRepositoryInterface;
use Exitialis\Mas\User;

class KeyRepository extends BaseRepository implements KeyRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function model()
    {
        return MasKey::class;
    }

    /**
     * Получить токен пользователя.
     *
     * @param User $user
     * @return mixed
     */
    public function getUserToken(User $user)
    {
        return $user->keys->session;
    }

    /**
     * Получить пользователя по uuid.
     *
     * @param $uuid
     * @return mixed
     */
    public function findUserByUuid($uuid)
    {
        return $this->findByField(compact('uuid'));
    }


}