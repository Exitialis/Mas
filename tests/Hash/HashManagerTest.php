<?php

use Exitialis\Mas\Managers\Hash\Drivers\HashContract;
use Exitialis\Mas\Managers\Hash\Drivers\WPHash;
use Exitialis\Mas\Managers\Hash\HashManager;
use Exitialis\Mas\Tests\TestCase;

class HashManagerTest extends TestCase
{
    /**
     * Создан инстанс шифровщика.
     */
    public function testHashInstanceCreated()
    {
        $manager = new HashManager('wp');
        $hash = $manager->make();

        $this->assertInstanceOf(HashContract::class, $hash);
        $this->assertInstanceOf(WPHash::class, $hash);
    }

    /**
     * Если класс не найден - должно броситься исключение.
     *
     *  @expectedException Exitialis\Mas\Managers\Hash\HashException
     */
    public function testClassDoesNotExistsExceptionWasThrown()
    {
        $manager = new HashManager('test');
        $hash = $manager->make();
    }

    /**
     * Проверяем работоспособность __call HashManager'a.
     */
    public function testHashCall()
    {
        $manager = new HashManager('wp');

        $hash = $manager->hash('test');

        $this->assertTrue($manager->checkValue('test', $hash));
    }

    /**
     * @expectedException Exitialis\Mas\Managers\Hash\HashException
     */
    public function testHashCallingWithUndefinedMethodThrowAnException()
    {
        $manager = new HashManager('wp');

        $hash = $manager->test('test');
    }

    /**
     * Тест, который воспроизводил баг с вызовом методов из менеджера.
     */
    public function testHashManagerReturnFalseWhenCheckValueWasCalled()
    {
        $manager = app()->make(HashManager::class);

        $string = 'test';
        $hash = $manager->hash($string);

        $this->assertTrue($manager->checkValue($string, $hash));
    }

}