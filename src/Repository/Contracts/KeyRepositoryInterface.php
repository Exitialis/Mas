<?php

namespace Exitialis\Mas\Repository\Contracts;

use Exitialis\Mas\User;

interface KeyRepositoryInterface
{
    /**
     * Получтиь токен пользователя.
     *
     * @param User $user
     * @return mixed
     */
    public function getUserToken(User $user);

    /**
     * Получить пользователя по uuid.
     *
     * @param $uuid
     * @return mixed
     */
    public function findByUuid($uuid);
}