<?php

namespace Exitialis\Mas\Repository\Contracts;

interface UserRepositoryInterface
{
    /**
     * Найти пользователя по логину.
     *
     * @param $login
     * @return mixed
     */
    public function findByLogin($login);
}