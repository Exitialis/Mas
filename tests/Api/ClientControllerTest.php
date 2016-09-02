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
        ])->seeStatusCode(204)->seeInDatabase('mas_keys', [
            'user_id' => $this->user->getKey(),
            'serverId' => $this->serverId
        ]);
    }
    
    public function testHasJoinedEndpoint()
    {
        $manager = new TexturesManager(config('mas.textures'));
        $textures = $manager->getTextures($this->user, $this->key);

        $this->key->serverid = $this->serverId;
        $this->key->save();

        $reponse = $this->get(route('mas.hasJoined', [
            'username' => $this->key->username,
            'serverId' => $this->serverId,
        ]),[
            'Accept' => 'application/json'
        ])->seeStatusCode(200)->dontSeeJson(["error" => "Bad login", "errorMessage" => "Bad Login"])->seeJson([
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
    
}