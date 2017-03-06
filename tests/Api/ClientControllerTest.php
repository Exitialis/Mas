<?php

use Exitialis\Mas\Exceptions\TexturesException;
use Exitialis\Mas\Managers\TexturesManager;
use Exitialis\Mas\MasKey;
use Exitialis\Mas\Tests\DbTestCase;
use Exitialis\Mas\User;
use Exitialis\Mas\Tests\TestCase;

class ClientControllerTest extends DbTestCase
{

    /**
     * Ключи пользователя.
     *
     * @var MasKey
     */
    protected $key;

    /**
     * Рандомная строка serverId.
     * 
     * @var string
     */
    protected $serverId;

    /**
     * Настройк для тестирования.
     */
    public function setUp()
    {
        parent::setUp();

        $key = factory(MasKey::class)->create();

        $this->user = $key->user;
        $this->key = $key;

        $this->serverId = str_random(32);
    }

    /**
     * Проверяем mas.join роут.
     */
    public function testJoinEndpoint()
    {
        $user_hash = $this->key->user_hash;
        $accessToken = $this->key->session;

        $this->post(route('mas.join'), [
            'selectedProfile' => $user_hash,
            'accessToken' => $accessToken,
            'serverId' => $this->serverId,
        ])->assertStatus(204);

        $this->assertDatabaseHas('mas_keys', [
            'user_id' => $this->user->getKey(),
            'serverId' => $this->serverId
        ]);
    }
    
    public function testHasJoinedEndpoint()
    {
        if ( ! file_exists(public_path(config('mas.textures.path.skin')))) {
            mkdir(public_path(config('mas.textures.path.skin')), 0777, true);
        }
        if ( ! file_exists(public_path(config('mas.textures.path.cloak')))) {
            mkdir(public_path(config('mas.textures.path.cloak')), 0777, true);
        }

        $manager = new TexturesManager(config('mas.textures'));
        $textures = $manager->getTextures($this->user, $this->key);

        $this->key->serverid = $this->serverId;
        $this->key->save();

        $this->get(route('mas.hasJoined', [
            'username' => $this->key->username,
            'serverId' => $this->serverId,
        ]),[
            'Accept' => 'application/json'
        ])->assertStatus(200)->assertDontSee(["error" => "Bad login", "errorMessage" => "Bad Login"])->assertJson([
            'id' => $this->key->uuid,
            'name' => $this->key->username,
            'properties' => array(
                [
                'name' => 'textures',
                'value' => base64_encode($textures),
                'signature' => 'Cg=='
                ]
            ),
        ]);
    }

    public function testHasJoinedEndpointShouldReturnErrorIfTexturesFolderDontCreated()
    {
        if (file_exists(public_path(config('mas.textures.path.skin')))) {
            $this->rmdir_recursive(public_path(config('mas.textures.path.skin')));
        }
        if (file_exists(public_path(config('mas.textures.path.cloak')))) {
            $this->rmdir_recursive(public_path(config('mas.textures.path.cloak')));
        }

        $manager = new TexturesManager(config('mas.textures'));

        try {
            $manager->getTextures($this->user, $this->key);
        } catch (TexturesException $e) {
            $textures = 'Error: ' . $e->getMessage();
        }

        $this->key->serverid = $this->serverId;
        $this->key->save();

        $this->get(route('mas.hasJoined', [
            'username' => $this->key->username,
            'serverId' => $this->serverId,
        ]),[
            'Accept' => 'application/json'
        ])->assertStatus(200)->assertDontSee(["error" => "Bad login", "errorMessage" => "Bad Login"])->assertJson([
            'id' => $this->key->uuid,
            'name' => $this->key->username,
            'properties' => array(
                [
                    'name' => 'textures',
                    'value' => base64_encode($textures),
                    'signature' => 'Cg=='
                ]
            ),
        ]);
    }

    private function rmdir_recursive($dir) {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) $this->rmdir_recursive("$dir/$file");
            else unlink("$dir/$file");
        }
        rmdir($dir);
    }

    /*private function makeFakeConfig()
    {
        $config = config('mas');

        $config['textures']['path']['skin'] = 'test';
        $config['textures']['path']['cloak'] = 'test';

        $str = '<?php 
            return [
        
        ';

        $this->writeConfig($config, $str);

        $str .= '];';

        rename(config_path('mas'), config_path('mas_real'));

        file_put_contents(config_path('mas'), $str);
    }

    private function writeConfig($config, &$str)
    {
        foreach ($config as $key => $value) {
            $str .= $key . '=> ';

            if (is_array($value)) {
                $str .= '[ ' . PHP_EOL;
                $this->writeConfig($value, $str);
                $str .= ']' . PHP_EOL;
            } else {
                $str .= $value . PHP_EOL;
            }
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        rename(config_path('mas_real'), config_path('mas'));
    }*/
}