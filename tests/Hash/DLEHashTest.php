<?php

use Exitialis\Mas\Managers\Hash\Drivers\DLEHash;
use Exitialis\Mas\Managers\Hash\HashManager;
use Exitialis\Mas\Tests\TestCase;

class DLEHashTest extends TestCase
{
    /**
     * Инстанс шифровальщика для WordPress.
     *
     * @var DLEHash
     */
    protected $hash;

    /**
     * Настройка шифровальщика.
     */
    public function setUp()
    {
        parent::setUp();

        $this->hash = (new HashManager('dle'))->make();
    }

    /**
     * Проверить на значении, верно ли выполнен Hash.
     */
    public function testCheckHashIsSameAsTestValue()
    {
        $string = 'test';
        $right = 'fb469d7ef430b0baf0cab6c436e70375';

        $hash = $this->hash->hash($string);

        $this->assertEquals($hash, $right);
    }

    /**
     * Проверяем, что значение было захешированно успешно и также успешно прошло валидацию.
     */
    public function testValueHasSuccessfullyHashed()
    {
        $string = $this->faker->password;

        $hash = $this->hash->hash($string);

        $this->assertTrue($this->hash->checkValue($string, $hash));
    }

    /**
     * Проверяем правильность проверки хэша.
     */
    public function testValueHasSuccessfullyChecked()
    {
        $string = 'test';
        $right = 'fb469d7ef430b0baf0cab6c436e70375';

        $this->assertTrue($this->hash->checkValue($string, $right));
        $this->assertFalse($this->hash->checkValue($string, str_random(32)));
    }
}