<?php

namespace Exitialis\Mas\Repositories;

use Illuminate\Database\Eloquent\Model;
use Mockery\CountValidator\Exception;

abstract class BaseRepository
{
    /**
     * Имя модели.
     *
     * @var string
     */
    protected $modelName;

    /**
     * Инстанс модели.
     *
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     * @param string $modelName
     */
    public function __construct($modelName)
    {
        $this->modelName = $modelName;
        $this->makeModel();
    }

    /**
     * Создание объекта модели по пути до нее.
     *
     * @return mixed
     */
    protected function makeModel()
    {
        $model = new $this->modelName;

        if (! $model instanceof Model) {
            throw new Exception('Model must be instance of model');
        }

        return $this->model = $model;
    }
}