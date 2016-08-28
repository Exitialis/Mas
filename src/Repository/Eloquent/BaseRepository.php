<?php

namespace Exitialis\Mas\Repository\Eloquent;

use Closure;
use Exitialis\Mas\Exceptions\RepositoryException;
use Exitialis\Mas\Repository\Contracts\RepositoryInterface;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BaseRepository
 * @package Prettus\Repository\Eloquent
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Application
     */
    protected $app;
    
    /**
     * @var Model
     */
    protected $model;
    
    /**
     * @var array
     */
    protected $fieldSearchable = [];
    
    /**
     * @var \Closure
     */
    protected $scopeQuery = null;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Обновить модель.
     *
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Установить класс модели.
     *
     * @return string
     */
    abstract public function model();

    /**
     * Создать инстанс модели.
     *
     * @return Model
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if ( ! $model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Поулчить поля, доступные для поиска.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Установить scope.
     *
     * @param \Closure $scope
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Получить ассоциативный массив данных из полей.
     *
     * @param string      $column
     * @param string|null $key
     * @return \Illuminate\Support\Collection|array
     */
    public function lists($column, $key = null)
    {
        return $this->model->pluck($column, $key);
    }

    /**
     * Получить все сущности из базы.
     *
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyScope();

        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        $this->resetModel();
        $this->resetScope();

        return $results;
    }

    /**
     * Получить первый элемент из репозитория.
     *
     * @param array $columns
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        $this->applyScope();

        $results = $this->model->first($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Получить пагинацию данных из репозитория.
     *
     * @param null $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'])
    {
        $this->applyScope();
        $limit = is_null($limit) ? 15 : $limit;

        $results = $this->model->paginate($limit, $columns);

        $this->resetModel();
        return $results;
    }

    /**
     * Найти сущность по id.
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyScope();

        $model = $this->model->find($id, $columns);

        $this->resetModel();
        return $model;
    }

    /**
     * Найти сущность по id, или выбросить исключение.
     *
     * @throws NotFoundHttpException
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $this->applyScope();

        $model = $this->model->findOrFail($id, $columns);

        $this->resetModel();
        return $model;
    }

    /**
     * Найти сущности по полю.
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->applyScope();

        $model = $this->model->where($field, $value)->get($columns);

        $this->resetModel();
        return $model;
    }

    /**
     * Найти данные по нескольким полям.
     *
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyScope();
        $this->applyConditions($where);

        $model = $this->model->first($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Найти данные по значениям.
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereIn($field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereIn($field, $values)->first($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Найти данные по исключению множества значений.
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereNotIn($field, $values)->first($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Создать новую сущность в базе.
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        $model = $this->model->newInstance($attributes);
        $model->save();

        $this->resetModel();

        return $model;
    }

    /**
     * Обновить сущность в базе по $id и заданным параметрам.
     *
     * @param array $attributes
     * @param integer $id
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        $model = $this->findOrFail($id);

        $model->update($attributes);

        $this->resetModel();

        return $model;
    }

    /**
     * Обновить или создать сущность в репозитории.
     *
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $this->applyScope();

        $model = $this->model->updateOrCreate($attributes, $values);

        $this->resetModel();

        return $model;
    }

    /**
     * Удалить сущность по заданному id.
     *
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        $this->applyScope();

        $model = $this->find($id);

        $this->resetModel();
        $deleted = $model->delete();

        return $deleted;
    }

    /**
     * Удалить сущность по значению поиска.
     *
     * @param array $where
     * @return mixed
     */
    public function deleteWhere(array $where)
    {
        $this->applyScope();

        $this->applyConditions($where);

        $deleted = $this->model->delete();

        $this->resetModel();

        return $deleted;
    }

    /**
     * Проверить, имеет ли сущность отношение.
     *
     * @param string $relation
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * Загрузить отношение.
     *
     * @param array|string $relations
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Загрузить связи с использованием callback.
     *
     * @param string $relation
     * @param closure $closure
     * @return $this
     */
    function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Установить скрытые от вывода поля модели.
     *
     * @param array $fields
     * @return $this
     */
    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);

        return $this;
    }

    /**
     * Отсортировать по столбцу.
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * Установить видимые поля для преобразования на странице.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields)
    {
        $this->model->setVisible($fields);

        return $this;
    }

    /**
     * Сбросить Query Scope
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Применить текущий scope на модель.
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    /**
     * Применить условия к модели.
     *
     * @param array $where
     * @return void
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }
}
