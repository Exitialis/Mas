<?php

namespace Exitialis\Mas\Managers\Hash\Drivers;

use Hautelook\Phpass\PasswordHash;

class WPHash implements HashContract
{
    /**
     * Библиотека для хэширования паролей в wordpress.
     *
     * @var PasswordHash
     */
    protected $library;

    /**
     * WPHash constructor.
     */
    public function __construct()
    {
        $this->library = new PasswordHash(8, false);
    }

    /**
     * Захэшировать строку.
     *
     * @param $value
     * @return string
     */
    public function hash($value)
    {
        return $this->library->HashPassword($value);
    }

    /**
     * Проверить значение на совпадение с хешем.
     *
     * @param $value
     * @param $hash
     * @return bool
     */
    public function checkValue($value, $hash)
    {
        return $this->library->CheckPassword($value, $hash);
    }

}