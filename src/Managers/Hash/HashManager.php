<?php


namespace Exitialis\Mas\Managers\Hash;

use Exitialis\Mas\Managers\Hash\Drivers\HashContract;
use Exitialis\Mas\Managers\Hash\Drivers\HasherContract;
use Illuminate\Foundation\Application;

class HashManager
{

    /**
     * Используемый hash.
     *
     * @var array
     */
    protected $hash;

    /**
     * HashManager constructor.
     * @param string $hash
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Создать шифратор.
     * @return mixed
     *
     * @throws HashException
     */
    public function make()
    {
        $class = $this->getHashDriverName();
        $hash = new $class;

        if ( ! $hash instanceof HashContract) {
            throw new HashException('Class ' . $this->getHashDriverName() . ' must implement HasherContract');
        }

        return $hash;
    }

    /**
     * Получить класс шифратора.
     *
     * @return string
     * @throws HashException
     */
    protected function getHashDriverName()
    {
        $namespace = __NAMESPACE__ . '\Drivers\\';
        $class = $namespace . strtoupper($this->hash) . 'Hash';
        
        if ( ! class_exists($class)) {
            throw new HashException('Class ' . $class . ' does not exists');
        }

        return $class;
    }
}