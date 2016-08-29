<?php

use Exitialis\Mas\Tests\DbTestCase;
use Exitialis\Mas\User;
use Exitialis\Mas\Tests\TestCase;

class ClientControllerTest extends DbTestCase
{
    public function testJoinEndpoint()
    {
        $uuid = $this->user->keys->uuid;
        $accessToken = $this->user->keys->session;
        $serverId = str_random(32);

        $this->post(route('mas.join'), [
            'selectedProfile' => $uuid,
            'accessToken' => $accessToken,
            'serverId' => $serverId,
        ])->seeStatusCode(204)->dontSeeJson(['error' => 'user not found'])->seeInDatabase('mas_keys', [
            'user_id' => $this->user->getKey(),
            'serverId' => $serverId
        ]);
    }
    
}