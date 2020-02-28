<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

abstract class Repository implements RepositoryInterface
{
    /**
     * Object of particular model
     *
     * @var object
     */
    protected $model;

    /**
     * Method to get all the records from the database.
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Method to create new record.
     *
     * @param array $attributes
     * @return collection
     */
    public function create(array $attributes) : Collection
    {
        return $this->model->create($attributes);
    }

    /**
     * Method to insert multiple records at once.
     *
     * @param  array $records
     * @return mixed
     */
    public function insertMultipleRows(array $records)
    {
        return $this->model->insert($records);
    }

    /**
     * Method to find record by its primary key.
     *
     * @param int $id
     * @return collection
     */
    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Method to update existing record.
     * It will not use "mass update" via eloquent, so that it will fire eloquent events while updating.
     *
     * @param  int $id
     * @param  array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        $currentModel = $this->find($id);

        return $currentModel->update($attributes);
    }

    /**
     * Method to update existing record via where condition.
     * It will use "mass update" via eloquent, so it will not fire eloquent events while updating.
     *
     * @param  array $where
     * @param  array $attributes
     * @return bool
     */
    public function updateWhere(array $where, array $attributes): bool
    {
        return $this->model->where($where)->update($attributes);
    }

    /**
     * Method to delete a record.
     * It will not use "mass delete" via eloquent.
     *
     * @param  int $id
     * @return bool
     */
    public function delete($id): bool
    {
        $currentModel = $this->find($id);

        return $currentModel->delete();
    }

    /**
     * To delete record by matching multiple attributes
     *
     * @param  array $attributes
     * @return bool
     */
    public function deleteBy(array $attributes): bool
    {
        return $this->model->where($attributes)->delete();
    }

    /**
     * Method to update/create the records.
     *
     * @param  array $whereAttributes
     * @param  array $insertAttributes
     * @return mixed
     */
    public function updateOrCreate(array $whereAttributes, array $insertAttributes)
    {
        return $this->model->updateOrCreate($whereAttributes, $insertAttributes);
    }

    /**
     * Method to update/create the records.
     *
     * @param  array $attributes
     * @return mixed
     */
    public function firstOrCreate(array $attributes)
    {
        return $this->model->firstOrCreate($attributes);
    }

    /**
     * To get all records by particular conditions
     *
     * @param  array $attributes
     * @return collection
     */
    public function whereBy(array $attributes): Collection
    {
        return $this->model->where($attributes)->get();
    }

    /**
     * To get one record from where condition.
     *
     * @param  array $attributes
     * @return mixed
     */
    public function whereByFirst(array $attributes)
    {
        return $this->model->where($attributes)->first();
    }

    /**
     * Method to get the model count.
     *
     * @param  array $where
     * @return mixed
     */
    public function getCount(array $where = [])
    {
        if (empty($where)) {
            return $this->model->count();
        }

        return $this->model->where($where)->count();
    }

    /**
     * Method to get search result by value.
     *
     * @param  $query
     * @param  array $allowedFields
     * @return mixed
     */
    public function attachSearchResult(&$query, array $allowedFields = [])
    {
        $searchValue = '';

        if (request()->has('q')) {
            $searchValue = trim(request()->get('q'));
        }

        if (empty($searchValue)) {
            return $query;
        }

        $query->attachSearch($allowedFields, $searchValue);

        return $query;
    }
}
