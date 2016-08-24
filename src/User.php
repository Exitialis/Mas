<?php

namespace Exitialis\Mas;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
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
    }
    
    /**
     * Костыль для получения логина и пароля для разных баз данных.
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key == 'login') {
            $login_column = config('mas.repositories.user.login_column');
            return $this->$login_column;
        } elseif ($key == 'password') {
            $password_column = config('mas.repositories.user.password_column');
            return $this->$password_column;
        }

        parent::__get($key);
    }
}