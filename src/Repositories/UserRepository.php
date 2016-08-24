<?php

namespace Exitialis\Mas\Repositories;

class UserRepository extends BaseRepository
{
    /**
     * Имя колонки пользователя в базе данных.
     *
     * @var string
     */
    protected $loginColumn;

    /**
     * Имя колонки с паролем в базе данных.
     *
     * @var string
     */
    protected $passwordColumn;

    /**
     * UserRepository constructor.
     * @param string $modelName
     * @param array $config
     */
    public function __construct($modelName, array $config)
    {
        parent::__construct($modelName);

        $this->loginColumn = $config['login_column'];
        $this->passwordColumn = $config['password_column'];
    }

    /**
     * Найти пользователя по логину.
     *
     * @param $login
     * @return mixed
     */
    public function findByLogin($login)
    {
        return $this->model->where($this->loginColumn, $login)->first();
    }
}