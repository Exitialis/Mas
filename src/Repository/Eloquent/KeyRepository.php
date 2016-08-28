<?php

namespace Exitialis\Mas\Repository\Eloquent;

use Exitialis\Mas\MasKey;
use Exitialis\Mas\Repository\Contracts\RepositoryInterface;
use Exitialis\Mas\User;

class KeyRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function model()
    {
        return MasKey::class;
    }
    
}