<?php

use Exitialis\Mas\Repository\Contracts\KeyRepositoryInterface;
use Exitialis\Mas\Repository\Contracts\UserRepositoryInterface;
use Exitialis\Mas\Tests\DbTestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BaseRepositoryTest extends DbTestCase
{
    /*
     * Инстанс репозитория для тестов.
     *
     * @var KeyRepositoryInterface
     */
    protected $repository;

    /**
     * SetUp variables.
     */
    public function setUp()
    {
        parent::setUp();

        $this->repository = app(KeyRepositoryInterface::class);

       
    }

    
    public function testListsReturnListOfData()
    {
        
    }
    
    
}

