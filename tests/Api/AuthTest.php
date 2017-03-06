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
        ])->assertStatus(200)->assertDontSee('false')
            ->assertSee($this->user->keys->uuid . '::' . $this->user->keys->session);

        $this->assertDatabaseHas('mas_keys', [
            'user_id' => $this->user->getKey()
        ]);
    }

    /**
     * Авторизация с учетом регистра не должна проходить, если регистр введенных данных не совпадает с регистром в бд.
     */
    public function testAuthShouldCheckCaseOfWords()
    {
        $this->post(route('mas.auth'), [
            'login' => strtoupper($this->user->login),
            'password' => '12345'
        ], ['Accept' => 'application/json'])->assertStatus(200)->assertSee('false');

        $this->assertDatabaseMissing('mas_keys', [
            'user_id' => $this->user->getKey()
        ]);
    }

    /**
     * Проверяем валидацию
     */
    public function testAuthLoginValidation()
    {
        $this->post(route('mas.auth'), [
            'login' => ''
        ], ['Accept' => 'application/json'])->assertStatus(422);

        $this->assertDatabaseMissing('mas_keys', [
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
        ], ['Accept' => 'application/json'])->assertStatus(422);

        $this->assertDatabaseMissing('mas_keys', [
            'user_id' => $this->user->getKey()
        ]);
    }
}