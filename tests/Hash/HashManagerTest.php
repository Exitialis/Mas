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


}