<?php

use Exitialis\Mas\Managers\TexturesManager;
use Exitialis\Mas\Tests\DbTestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\File;

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

        $this->manager = new TexturesManager(config('mas.textures'));
    }

    /**
     * Тест получения ссылки на скин пользователя.
     */
    public function testGettingSkinUrl()
    {
        $rightPath = asset(config('mas.textures.path.skin_path') . '/' . $this->user->login . '.png');
        $skin = $this->manager->getSkin($this->user);
        
        $this->assertEquals($skin, $rightPath);
    }
    
    public function testGettingDefaultSkinUrl()
    {
        $rightPath = asset(config('mas.textures.path.skin_path') . '/' . config('mas.textures.skin_default') . '.png');
        $skin = $this->manager->getSkin($this->user);

        $this->assertEquals($skin, $rightPath);
    }

    /**
     * Тест получения ссылки на плащ пользователя.
     */
    public function testGettingCapeUrl()
    {
        $rightPath = asset(config('mas.textures.path.cloak_path') . '/' . $this->user->login . '.png');
        $skin = $this->manager->getCape($this->user);

        $this->assertEquals($skin, $rightPath);
    }
}