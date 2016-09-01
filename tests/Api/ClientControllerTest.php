<?php

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
    protected $keys;

    protected $serverId;

    /**
     * Настройк для тестирования.
     */
    public function setUp()
    {
        parent::setUp();

        $this->keys = factory(MasKey::class)->create([
            'user_id' => $this->user->getKey()
        ]);

        $this->serverId = str_random(32);
    }

    /**
     * Проверяем mas.join роут.
     */
    public function testJoinEndpoint()
    {
        $uuid = $this->user->keys->uuid;
        $accessToken = $this->user->keys->session;

        $this->post(route('mas.join'), [
            'selectedProfile' => $uuid,
            'accessToken' => $accessToken,
            'serverId' => $this->serverId,
        ])->seeStatusCode(204)->seeInDatabase('mas_keys', [
            'user_id' => $this->user->getKey(),
            'serverId' => $this->serverId
        ]);
    }
    
    public function testHasJoinedEndpoint()
    {
        $manager = new TexturesManager(config('mas.textures'));
        
        $this->get(route('mas.hasJoined', [
            'username' => $this->user->login,
            'serverId' => $this->serverId,
        ]),[
            'Accept' => 'application/json'
        ])->seeStatusCode(200)->dontSeeJson(["error" => "Bad login", "errorMessage" => "Bad Login"])->seeJson([
            'id' => $this->user->keys->user_hash,
            'name' => $this->user->login,
            'properties' => [
                'name' => 'textures',
                'value' => base64_encode($manager->getTextures($this->user)),
                'signature' => 'Cg=='
            ],
        ]);
    }
    
}