<?php

namespace Exitialis\Tests;

use Exitialis\Mas\Tests\DbTestCase;
use Illuminate\Foundation\Testing\TestCase;

class AuthTest extends DbTestCase
{
    /**
     * Проверяем авторизацию с верными данными.
     */
    public function testAuthSuccessWithRightCredentials()
    {
        $this->post(route('mas.auth'), [
            'login' => $this->user->login,
            'password' => '12345'
        ])->seeStatusCode(200)->dontSee('false')->seeInDatabase('mas_keys', [
            'user_id' => $this->user->getKey()
        ])->see($this->user->keys->uuid . '::' . $this->user->keys->session);
    }

    /**
     * Проверяем валидацию
     */
    public function testAuthLoginValidation()
    {
        $this->post(route('mas.auth'), [
            'login' => ''
        ], ['Accept' => 'application/json'])->seeStatusCode(422)->dontSeeInDatabase('mas_keys', [
            'user_id' => $this->user->getKey()
        ]);
    }

    /**
     * Проверяем валидацию
     */
    public function testAuthPasswordValidation()
    {
        $this->post(route('mas.auth'), [
            'password' => ''
        ], ['Accept' => 'application/json'])->seeStatusCode(422)->dontSeeInDatabase('mas_keys', [
            'user_id' => $this->user->getKey()
        ]);
    }
}