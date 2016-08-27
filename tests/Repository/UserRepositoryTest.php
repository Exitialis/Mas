<?php

namespace Exitialis\Tests;

use Exitialis\Mas\Repositories\UserRepository;

class UserRepositoryTest extends MasTestCase
{

    protected $users;

    public function setUp(UserRepository $users)
    {
        parent::setUp();

        $this->users = $users;
    }

    public function testModelProvided()
    {

    }

    public function testUserCreated()
    {

    }
}