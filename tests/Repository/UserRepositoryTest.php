<?php

use Exitialis\Mas\Tests\DbTestCase;

class UserRepositoryTest extends DbTestCase
{

	/**
	 * Репозиторий пользователей.
	 * @var UserRepository
	 */
	protected $repository;


	public function setUp()
	{
		parent::setUp();

		$this->repository = $this->app[Exitialis\Mas\Repository\Contracts\UserRepositoryInterface::class];
	}

	public function testAuthRetrieveWrongUser()
    {
    	$users = factory(Exitialis\Mas\User::class, 10)
    		->create()
    		->each(function ($u) {
                $u->keys()->save(factory(Exitialis\Mas\MasKey::class)->make());
            });

        $user = $users[4];

        $finded = $this->repository->findByLogin($user->login);

        $this->assertEquals($finded->getKey(), $user->getKey());
    }

}