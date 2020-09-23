<?php

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository implements IBaseRepository
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
     * @var \Closure
     */
    protected $scopeQuery = null;

    /**
     * @var bool
     */
    protected $usesSoftDeletes;

    /**
     * BaseRepository constructor.
     * @param Application $app
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * @return Model|mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of " . Model::class . ".");
        }
        $this->usesSoftDeletes = in_array(SoftDeletes::class, class_uses($model));

        return $this->model = $model;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * Query Scope
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Retrieve data array for populate field select
     *
     * @param string      $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists($column, $key = null)
    {
        return $this->model->lists($column, $key);
    }

    public function join(string $relationship, string $column1, string $operator, string $column2)
    {
        $this->model = $this->model->join($relationship, $column1, $operator, $column2);

        return $this;
    }

    public function leftJoin(string $relationship, string $column1, string $operator, string $column2)
    {
        $this->model = $this->model->leftJoin($relationship, $column1, $operator, $column2);

        return $this;
    }

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3
     *
     * @param string $column
     * @param null   $key
     * @return array|Collection
     * @throws RepositoryException
     */
    public function pluck($column, $key = null)
    {
        $this->applyScope();
        $model = $this->model->pluck($column, $key);
        $this->resetModel();

        return $model;
    }

    /**
     * Retrieve all data of repository
     *where
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     * @throws RepositoryException
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
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function first($columns = ['*'])
    {
        $this->applyScope();

        $results = $this->model->first($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Retrieve last data of repository
     *
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function last($columns = ['*'])
    {
        $this->applyScope();

        $results = $this->model->orderBy('create_time', 'DESC')->first($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Retrieve first data of repository, or create new Model
     *
     * @param array $where
     * @param array $attributes
     * @return mixed
     * @throws RepositoryException
     */
    public function firstOrCreate(array $where, array $attributes = [])
    {
        $this->applyScope();

        $model = $this->model->firstOrCreate($where, $attributes);

        $this->resetModel();

        return $model;
    }

    /**
     * Update an existing model or create a new model
     *
     * @param array $where
     * @param array $values
     * @return mixed
     * @throws RepositoryException
     */
    public function updateOrCreate(array $where, array $values)
    {
        $this->applyScope();

        $model = $this->model->updateOrCreate($where, $values);

        $this->resetModel();

        return $model;
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null  $limit
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function simplePaginate($limit = null, $columns = ['*'])
    {
        return $this->paginate($limit, $columns, "simplePaginate");
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null   $limit
     * @param array  $columns
     * @param string $method
     * @return mixed
     * @throws RepositoryException
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $this->applyScope();
        $limit = is_null($limit) ? config('repository.pagination.limit', 15) : $limit;
        $results = $this->model->{$method}($limit, $columns);
        $results->appends(app('request')->query());
        $this->resetModel();

        return $results;
    }

    /**
     * Find data by field and value
     *
     * @param       $field
     * @param null  $value
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Chained orWhere method
     * @param array $where
     * @return $this
     */
    public function orWhere(array $where)
    {
        $this->applyConditionsOr($where);

        return $this;
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyScope();

        $this->applyConditions($where);

        $model = $this->model->get($columns);
        $this->resetModel();

        return $model;
    }

    public function findOne(array $where, $columns = ['*'])
    {
        $this->applyScope();

        $this->applyConditions($where);

        $model = $this->model->first($columns);

        $this->resetModel();

        return $model;
    }

    /**
     * Find data by multiple values in one field
     *
     * @param       $field
     * @param       $values
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function findWhereIn($field, $values, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find data by excluding multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Order collection by a given column
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * @param $rawExpression
     * @return $this|mixed
     */
    public function orderByRaw($rawExpression)
    {
        $this->model = $this->model->orderByRaw($rawExpression);

        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param string   $relation
     * @param \Closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param string   $relation
     * @param \Closure $closure
     *
     * @return $this
     */
    public function orWhereHas($relation, $closure)
    {
        $this->model = $this->model->orWhereHas($relation, $closure);

        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param string   $relation
     * @param \Closure $closure
     *
     * @return $this
     */
    public function whereDoesntHave($relation, \Closure $closure = null)
    {
        $this->model = $this->model->whereDoesntHave($relation, $closure);

        return $this;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Get count of relation
     *
     * @param string   $relation
     * @param \Closure $closure
     * @return mixed
     */
    public function withCount(string $relation, \Closure $closure = null)
    {
        $this->model = $this->model->withCount([$relation => $closure]);

        return $this;
    }

    /**
     * Save a new entity in repository
     *
     * @param $args
     * @return mixed
     * @throws RepositoryException
     */
    public function create($args)
    {
        $modelType = $this->model();
        if ($args instanceof $modelType) {
            $model = $args;
        } else {
            if (is_array($args) || $args instanceof \stdClass) {
                $model = $this->model->newInstance();
                foreach ($args as $key => $value) {
                    $model->{$key} = $value;
                }
            } else {
                throw new RepositoryException("Argument must be instance of array, stdClass or " . $this->model() .
                    ".");
            }
        }
        $model->save();
        $this->resetModel();

        return $model;
    }

    public function insert(array $args): bool
    {
        $status = $this->model->insert($args);

        $this->resetModel();

        return $status;
    }

    public function save($args): bool
    {
        $modelType = $this->model();
        if ($args instanceof $modelType) {
            $model = $args;
        } else {
            if (is_array($args) || $args instanceof \stdClass) {
                $model = $this->model->newInstance();
                foreach ($args as $key => $value) {
                    $model->{$key} = $value;
                }
            } else {
                throw new RepositoryException("Argument must be instance of array, stdClass or " . $this->model() .
                    ".");
            }
        }
        $status = $model->save();

        $this->resetModel();

        return $status;
    }

    /**
     * Update a entity in repository by id
     *
     * @param       $args
     * @param       $id
     * @return mixed
     * @throws RepositoryException
     */
    public function update($args, $id)
    {
        $this->applyScope();
        $modelType = $this->model();

        if ($args instanceof $modelType) {
            $model = $args;
        } else {
            $model = $this->model->find($id);
            if (is_null($model)) {
                $this->resetModel();

                return null;
            }
            if (is_array($args) || $args instanceof \stdClass) {
                foreach ($args as $key => $value) {
                    $model->{$key} = $value;
                }
            } else {
                throw new RepositoryException("Argument must be instance of array, stdClass or " . $this->model() .
                    ".");
            }
        }
        $model->save();
        $this->resetModel();

        return $model;
    }

    public function updateWhere(array $where, $updateArgs): bool
    {
        $this->applyScope();

        $this->applyConditions($where);

        $deleted = $this->model->update($updateArgs);
        $this->resetModel();

        return $deleted;
    }

    public function updateWhereIn(string $field, $values, $updateArgs): bool
    {
        $this->applyScope();

        $deleted = $this->model->whereIn($field, $values)->update($updateArgs);
        $this->resetModel();

        return $deleted;
    }

    /**
     * @param      $id
     * @param bool $softDelete
     * @return int
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function delete($id, bool $softDelete = false)
    {
        $this->applyScope();

        $model = $this->model->where($this->model->getKeyName(), $id);
        $this->resetModel();

        if ($softDelete && !$this->usesSoftDeletes) {
            $deleted = $model->update(['deleted' => 1]);
        } elseif (!$softDelete && $this->usesSoftDeletes) {
            $deleted = $model->forceDelete();
        } else {
            $deleted = $model->delete();
        }

        return $deleted;
    }

    /**
     * @param       $id
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->find($id, $columns);
        $this->resetModel();

        return $model;
    }

    public function having(string $column, string $operator, string $value)
    {

        $this->model = $this->model->having($column, $operator, $value);

        return $this;
    }

    public function havingRaw(string $expression)
    {

        $this->model = $this->model->havingRaw($expression);

        return $this;
    }

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     * @param bool  $softDelete
     * @return bool|int|null
     * @throws RepositoryException
     * @throws \Exception
     */
    public function deleteWhere(array $where, bool $softDelete = false)
    {
        $this->applyScope();

        $this->applyConditions($where);

        if ($softDelete && !$this->usesSoftDeletes) {
            $deleted = $this->model->update(['deleted_at' => now()]);
        } elseif (!$softDelete && $this->usesSoftDeletes) {
            $deleted = $this->model->forceDelete();
        } else {
            $deleted = $this->model->delete();
        }

        $this->resetModel();

        return $deleted;
    }

    /**
     * @param string           $field
     * @param Collection|array $values
     * @param bool             $softDelete
     * @return bool|int|null
     * @throws RepositoryException
     * @throws \Exception
     */
    public function deleteWhereIn(string $field, $values, bool $softDelete = false)
    {
        $this->applyScope();

        $this->whereIn($field, $values);

        if ($softDelete && !$this->usesSoftDeletes) {
            $deleted = $this->model->update(['deleted' => 1]);
        } elseif (!$softDelete && $this->usesSoftDeletes) {
            $deleted = $this->model->forceDelete();
        } else {
            $deleted = $this->model->delete();
        }

        $this->resetModel();

        return $deleted;
    }

    /**
     * Find data and return instance of self for chaining
     * @param array $where
     * @return mixed
     */
    public function where(array $where)
    {
        $this->applyConditions($where);

        return $this;
    }

    /**
     * @param string $field
     * @param array  $between
     * @return $this
     */
    public function whereBetween(string $field, array $between)
    {
        $this->model = $this->model->whereBetween($field, $between);

        return $this;
    }

    /**
     * @param string $field
     * @param array  $between
     * @return $this
     */
    public function orWhereBetween(string $field, array $between)
    {
        $this->model = $this->model->orWhereBetween($field, $between);

        return $this;
    }

    /**
     * Find data and return instance of self for chaining
     * @param string $where
     * @param array  $params
     * @return mixed
     */
    public function whereRaw(string $where, array $params = [])
    {
        $this->model = $this->model->whereRaw($where, $params);

        return $this;
    }

    /**
     * Find data and return instance of self for chaining
     * @param array|Collection $values
     * @param string           $field
     * @return mixed
     */
    public function whereIn(string $field, $values)
    {
        $this->model = $this->model->whereIn($field, $values);

        return $this;
    }

    /**
     * Find data and return instance of self for chaining
     * @param array|Collection $values
     * @param string           $field
     * @return mixed
     */
    public function whereNotIn(string $field, $values)
    {
        $this->model = $this->model->whereNotIn($field, $values);

        return $this;
    }

    /**
     * Retrieve results from query
     *
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function get($columns = ['*'])
    {
        $this->applyScope();

        $model = $this->model->get($columns);

        $this->resetModel();

        return $model;
    }

    /**
     * Add offset to query.
     *
     * @param $offset
     * @param $limit
     *
     * @return IBaseRepository
     */
    public function skipAndTake($offset, $limit): IBaseRepository
    {
        $this->model = $this->model->skip($offset * $limit)->take($limit);

        return $this;
    }

    /**
     * Add limit to results
     *
     * @param $limit
     * @return IBaseRepository
     */
    public function limit($limit): IBaseRepository
    {
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * Counts the query results
     *
     * @return mixed
     * @throws RepositoryException
     */
    public function count()
    {
        $this->applyScope();

        $model = $this->model->count();

        $this->resetModel();

        return $model;
    }

    /**
     * Sums a column
     * @param $field
     * @return mixed
     * @throws RepositoryException
     */
    public function sum($field)
    {
        $this->applyScope();

        $model = $this->model->sum($field);

        $this->resetModel();

        return $model;
    }

    public function increment($field, $value)
    {
        $this->applyScope();

        $model = $this->model->increment($field, $value);

        $this->resetModel();

        return $model;
    }

    public function decrement($field, $value)
    {
        $this->applyScope();

        $model = $this->model->decrement($field, $value);

        $this->resetModel();

        return $model;
    }

    public function distinct()
    {
        $this->model = $this->model->distinct();

        return $this;
    }

    public function groupBy($field)
    {
        $this->model = $this->model->groupBy($field);

        return $this;
    }

    public function groupByMultiple()
    {
        $this->model = call_user_func_array([$this->model, 'groupBy'], func_get_args());

        return $this;
    }

    /**
     * Checks if the model count is greater than value
     *
     * @param int $value
     * @return bool
     * @throws RepositoryException
     */
    public function countGreaterThanOrEqualTo(int $value): bool
    {
        $this->applyScope();

        $model = $this->model->count();

        $this->resetModel();

        return $model >= $value;
    }

    /**
     * @param string $relation
     * @param int    $id
     * @return mixed
     * @throws RepositoryException
     */
    public function attachRelation(string $relation, int $id)
    {
        $model = $this->model->attach($relation, $id);

        $this->resetModel();

        return $model;
    }

    /**
     * @param array  $where
     * @param string $column
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findMaxValueWhere(array $where, string $column)
    {
        $this->applyScope();

        $this->applyConditions($where);

        $model = $this->model->get($column);

        $maxValue = $model->max($column);

        $this->resetModel();

        return $maxValue;
    }

    /**
     * Apply scope in current Query
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
     * Applies the given where conditions to the model.
     *
     * @param array $where
     * @return void
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                [$field, $condition, $val] = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * @param array $where
     */
    protected function applyConditionsOr(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                [$field, $condition, $val] = $value;
                $this->model = $this->model->where($field, $condition, $val, 'or');
            } else {
                $this->model = $this->model->where($field, '=', $value, 'or');
            }
        }
    }
}
