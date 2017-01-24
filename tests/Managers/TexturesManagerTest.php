<?php

use Exitialis\Mas\Exceptions\TexturesException;
use Exitialis\Mas\Managers\TexturesManager;
use Exitialis\Mas\Tests\DbTestCase;

class TexturesManagerTest extends DbTestCase
{
    /**
     * Менеджер текстур.
     *
     * @var TexturesManager
     */
    protected $manager;

    /**
     * Настройка тестов.
     */
    public function setUp()
    {
        parent::setUp();

        $this->creatingDirs();
        $this->manager = new TexturesManager(config('mas.textures'));
    }

    /**
     * Тест получения ссылки на скин пользователя.
     */
    public function testGettingSkinUrl()
    {
        $login = $this->user->login;
        $path = public_path('textures/skin/' . $login . '.png');

        copy(__DIR__ . '/../test.png', $path);

        $rightPath = asset('cache' . '/' . md5($login . 'skin') . '.png');
        $skin = $this->manager->getSkin($this->user);
        unlink($path);

        $this->assertEquals($skin, $rightPath);
    }
    
    public function testGettingDefaultSkinUrl()
    {
        $rightPath = asset(config('mas.textures.path.skin') . '/' . config('mas.textures.skin_default.name') . '.png');
        $skin = $this->manager->getSkin($this->user);

        $this->assertEquals($skin, $rightPath);
    }

    /**
     * Тест получения ссылки на плащ пользователя.
     */
    public function testGettingCloakUrl()
    {
        $login = $this->user->login;
        $path = public_path('textures/cloak/' . $login . '.png');

        copy(__DIR__ . '/../test.png', $path);
        $rightPath = asset('cache' . '/' . md5($login . 'cloak') . '.png');
        $skin = $this->manager->getCloak($this->user);
        unlink($path);

        $this->assertEquals($skin, $rightPath);
    }

    /**
     * @expectedException Exitialis\Mas\Exceptions\TexturesException
     */
    public function testItShouldThrowExceptionIfSkinPathDoesNotExist()
    {
        $manager = new TexturesManager([
            'path' => [
                'skin' => 'test',
            ]
        ]);

        $manager->getSkin($this->user);
    }

    /**
     * @expectedException Exitialis\Mas\Exceptions\TexturesException
     */
    public function testItShouldThrowExceptionIfCloakPathDoesNotExist()
    {
        $manager = new TexturesManager([
            'path' => [
                'cloak' => 'test'
            ]
        ]);

        $manager->getCloak($this->user);
    }

    /**
     * Тест получения стандартого плаща, если плащ не установлен.
     */
    public function testGettingDefaultCloakUrl()
    {
        $rightPath = asset(config('mas.textures.path.cloak') . '/' . config('mas.textures.cloak_default.name') . '.png');
        $cloak = $this->manager->getCloak($this->user);

        $this->assertEquals($cloak, $rightPath);
    }

    public function testSkinDefaultActiveOptionShouldDeactivateGettingDefaultSkinForUser()
    {
        $manager = new TexturesManager([
            'skin_default' => [
                'active' => false,
                'name' => 'default'
            ],
            'path' => [
                'skin' => 'textures/skin'
            ]
        ]);

        $actual = $manager->getSkin($this->user);

        $this->assertEquals(false, $actual);
    }

    /**
     * Создать необходимые директории.
     */
    protected function creatingDirs()
    {
        $path = public_path();
        if ( ! file_exists(public_path('textures'))) {
            mkdir($path . '/textures');
        }
        if ( ! file_exists($path . '/textures/skin')) {
            mkdir($path . '/textures/skin');
        }
        if ( ! file_exists($path . '/textures/cloak')) {
            mkdir($path . '/textures/cloak');
        }
    }

}