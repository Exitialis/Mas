<?php

namespace Exitialis\Mas;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Первичный ключ в таблице пользователей.
     *
     * @var int
     */
    protected $primaryKey;
    /**
     * Имя таблицы с пользователями в базе данных.
     *
     * @var string
     */
    protected $table;

    /**
     * User constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('mas.repositories.user.table_name');
        $this->primaryKey = config('mas.repositories.user.key');
    }
    
    /**
     * Получение логина и пароля пользователя из базы в кросс-платформенной форме.
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key == 'login') {
            $login_column = config('mas.repositories.user.login_column');
            return $this->attributesToArray()[$login_column];
        } elseif ($key == 'password') {
            $password_column = config('mas.repositories.user.password_column');
            return $this->attributesToArray()[$password_column];
        }

        parent::__get($key);
    }
}