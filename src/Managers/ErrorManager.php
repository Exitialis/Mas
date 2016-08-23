<?php

namespace Exitialis\Mas\Managers;

/**
 * Работа с сообщениями об ошибках.
 */
class ErrorManager
{
    /**
     * Сообщения об ошибках.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Добавить сообщение об ошибке.
     *
     * @param  string $error
     * @return array
     */
    public function add($error)
    {
        if ( ! is_array($error)) {
            $error = [$error];
        }

        $this->errors = array_merge($this->errors, $error);
    }

    /**
     * Получить первое сообщение об ошибке.
     *
     * @return string
     */
    public function first()
    {
        if (count($this->errors) === 0) {
            return null;
        }

        return current($this->errors);
    }

    /**
     * Получить ошибку по названию.
     *
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        if (count($this->errors) === 0) {
            return null;
        }

        return $this->errors[$name];
    }

    /**
     * Получить все сообщения об ошибках.
     *
     * @return array
     */
    public function all()
    {
        return $this->errors;
    }
}
