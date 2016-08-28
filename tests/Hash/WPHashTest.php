<?php


use Exitialis\Mas\Managers\Hash\Drivers\WPHash;
use Exitialis\Mas\Managers\Hash\HashManager;
use Exitialis\Mas\Tests\TestCase;

class WPHashTest extends TestCase
{

    /**
     * Инстанс шифровальщика для WordPress.
     *
     * @var WPHash
     */
    protected $hash;

    /**
     * Настройка шифровальщика.
     */
    public function setUp()
    {
        parent::setUp();

        $this->hash = (new HashManager('wp'))->make();
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
        $string = 'tester';
        $right = '$P$BHvnOBqV6VV7L8VlFEigHCcjiuYaFk0';

        $this->assertTrue($this->hash->checkValue($string, $right));
        $this->assertFalse($this->hash->checkValue($string, str_random(34)));
    }
}