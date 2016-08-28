<?php


namespace Exitialis\Mas\Managers\Hash;

use Exitialis\Mas\Managers\Hash\Drivers\HashContract;
use Exitialis\Mas\Managers\Hash\Drivers\HasherContract;
use Illuminate\Foundation\Application;
use Mockery\CountValidator\Exception;

class HashManager
{

    /**
     * Используемый hash.
     *
     * @var array
     */
    protected $hash;

    /**
     * Инстанс хэшера.
     *
     * @var HashContract
     */
    protected $encrypt;

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
        if ($this->encrypt) {
            if ($this->encrypt instanceof HashContract) {
                return $this->encrypt;
            }
        }

        $class = $this->getHashDriverName();
        $hash = new $class;

        if ( ! $hash instanceof HashContract) {
            throw new HashException('Class ' . $this->getHashDriverName() . ' must implement HasherContract');
        }

        return $this->encrypt = $hash;
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

    /**
     * Вызов методов
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws HashException
     */
    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'hash':
                return $this->make()->{$name}($arguments[0]);
                break;
            case 'checkValue':
                return $this->make()->{$name}($arguments[0], $arguments[1]);
                break;
        }

        throw new HashException('Method ' . $name . ' not found in hash class');
    }
}