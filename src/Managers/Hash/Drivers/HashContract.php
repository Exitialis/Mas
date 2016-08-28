<?php

namespace Exitialis\Mas\Managers\Hash\Drivers;

interface HashContract
{
    /**
     * Захэшировать строку.
     *
     * @param $value
     * @return string
     */
    public function hash($value);

    /**
     * Проверить значение на совпадение с хешем.
     *
     * @param $value
     * @param $hash
     * @return bool
     */
    public function checkValue($value, $hash);
}