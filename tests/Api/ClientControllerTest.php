<?php

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

    /**
     * Настройк для тестирования.
     */
    public function setUp()
    {
        parent::setUp();

        $this->keys = factory(MasKey::class)->create([
            'user_id' => $this->user->getKey()
        ]);
    }

    /**
     * Проверяем mas.join роут.
     */
    public function testJoinEndpoint()
    {
        $uuid = $this->user->keys->uuid;
        $accessToken = $this->user->keys->session;
        $serverId = str_random(32);

        $this->post(route('mas.join'), [
            'selectedProfile' => $uuid,
            'accessToken' => $accessToken,
            'serverId' => $serverId,
        ])->seeStatusCode(204)->seeInDatabase('mas_keys', [
            'user_id' => $this->user->getKey(),
            'serverId' => $serverId
        ]);
    }
    
    public function testHasJoinedEndpoint()
    {
        
    }
    
}