<?php

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use Illuminate\Support\Collection;

interface IBaseRepository
{

    /**
     * Query Scope
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope);

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope();

    /**
     * Retrieve data array for populate field select
     *
     * @param string      $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists($column, $key = null);

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3
     * @param string      $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck($column, $key = null);

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*']);

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * Having
     * @param $column
     * @param $operator
     * @param $value
     * @return mixed
     */
    public function having(string $column, string $operator, string $value);

    /**
     * Having raw
     * @param string $expression
     * @return mixed
     */
    public function havingRaw(string $expression);

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function first($columns = ['*']);

    /**
     * Retrieve last data of repository
     * @param array $columns
     * @return mixed
     */
    public function last($columns = ['*']);

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     * @param array $where
     *
     * @return mixed
     */
    public function firstOrCreate(array $where, array $attributes = []);


    /**
     * Find data by field and value
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*']);

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*']);


    /**
     * Find data by excluding multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']);

    /**
     * Order collection by a given column
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc');

    /**
     * @param $rawExpression
     * @return mixed
     */
    public function orderByRaw($rawExpression);

    /**
     * Load relation with closure
     *
     * @param string   $relation
     * @param \Closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure);

    /**
     * Load relation with closure
     *
     * @param string   $relation
     * @param \Closure $closure
     *
     * @return $this
     */
    public function orWhereHas($relation, $closure);

    /**
     * Load relation with closure
     *
     * @param string   $relation
     * @param \Closure $closure
     *
     * @return $this
     */
    public function whereDoesntHave($relation, \Closure $closure = null);

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation);

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations);

    /**
     * Save a new entity in repository
     *
     * @param $args
     * @return mixed
     * @throws RepositoryException
     */
    public function create($args);

    /**
     * Mass insert in repository
     *
     * @param $args
     * @return mixed
     * @throws RepositoryException
     */
    public function insert(array $args): bool;

    /**
     * Save a new entity in repository
     *
     * @param $args
     * @return mixed
     * @throws RepositoryException
     */
    public function save($args): bool;  

    /**
     * Update a entity in repository by id
     *
     * @param       $args
     * @param       $id
     * @return mixed
     * @throws RepositoryException
     */
    public function update($args, $id);

    /**
     * Update a entity in repository by where clause
     *
     * @param array $updateArgs
     * @param array $where
     * @return bool
     * @throws RepositoryException
     */
    public function updateWhere(array $where, $updateArgs): bool;

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     * @param $softDelete
     * @return int
     */
    public function delete($id, bool $softDelete = false);


    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     * @param bool  $softDelete
     * @return int
     */
    public function deleteWhere(array $where, bool $softDelete = false);


    /**
     * Find data and return instance of self for chaining
     *
     * @param array $where
     * @return mixed
     */
    public function where(array $where);

    /**
     * @param string $field
     * @param array  $between
     * @return mixed
     */
    public function whereBetween(string $field, array $between);

    /**
     * @param string $field
     * @param array  $between
     * @return mixed
     */
    public function orWhereBetween(string $field, array $between);

    /**
     * Find or Data and return instance of self for chaining
     * @param array $where
     * @return mixed
     */
    public function orWhere(array $where);

    /**
     * Find data and return instance of self for chaining using raw query
     *
     * @param string $where
     * @param array  $params
     * @return mixed
     */
    public function whereRaw(string $where, array $params = []);

    /**
     * Retrieve results from query
     *
     * @param array $columns
     * @return mixed
     */
    public function get($columns = ['*']);

    /**
     * Limit results
     *
     * @param $limit
     * @return IBaseRepository
     */
    public function limit($limit): IBaseRepository;

 
    /**
     * Find first model where conditions are met
     *
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function findOne(array $where, $columns = ['*']);

}
