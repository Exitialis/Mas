<?php

namespace Exitialis\Mas\Managers\Hash\Drivers;

class DLEHash implements HashContract
{
    /**
     * Захэшировать строку.
     *
     * @param $value
     * @return string
     */
    public function hash($value)
    {
        return md5(md5($value));
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
        return $this->hash($value) === $hash;
    }

}