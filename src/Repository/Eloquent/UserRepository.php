<?php

namespace Exitialis\Mas\Repository\Eloquent;

use Exitialis\Mas\Repository\Contracts\UserRepositoryInterface;
use Exitialis\Mas\User;
use Illuminate\Foundation\Application;

class UserRepository extends BaseRepository implements UserRepositoryInterface
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
     *
     * @param Application $app
     * @param array $config
     */
    public function __construct(Application $app, array $config)
    {
        parent::__construct($app);

        $this->loginColumn = $config['login_column'];
        $this->passwordColumn = $config['password_column'];
    }

    /**
     * Задать класс для модели репозитория.
     *
     * @return mixed
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Найти пользователя по логину.
     *
     * @param $login
     * @return mixed
     */
    public function findByLogin($login)
    {
        return $this->findWhere([$this->loginColumn => $login]);
    }
    
    

}