<?php

use Exitialis\Mas\Managers\TexturesManager;
use Exitialis\Mas\Tests\DbTestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

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
        $rightPath = asset(config('mas.textures.path.skin') . '/' . config('mas.textures.skin_default') . '.png');
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
     * Создать необходимые директории.
     */
    protected function creatingDirs()
    {
        $path = public_path();
        if ( ! file_exists(public_path('textures/skin'))) {
            mkdir($path . '/textures');
            mkdir($path . '/textures/skin');
            mkdir($path . '/textures/cloak');
        }
    }

}