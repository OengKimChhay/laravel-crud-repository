<?php

namespace App\CrudRepository;

use Illuminate\Database\Eloquent\{Model, Builder, Collection};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class CrudRepository
{

    /**
     * the fields to select from database.
     * declare this var in your service.
     * @var array
     */
    protected array $select_fields = [];

    /**
     * declare relations 
     * @var array
     */
    protected array $allowRelations = [];

    /**
     * the instance of model.
     * @var Model
     */
    protected $model;

    /**
     * the limitation of data to get.
     * @var int
     */
    protected int $limit = 50;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Return primary key column fo the model
     * @return string
     */
    public function getPrimaryKeyName(): string
    {
        return $this->model->getKeyName();
    }

    /**
     * @param array $fields
     * @return array
     */
    public function getSelectFields($fields)
    {
        return empty($fields) ? ['*'] : array_map(fn ($field) => preg_replace('/[^a-zA-Z0-9_*]/', '', $field), $fields);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @return callable
     */
    private function buildFiltersQuery(string $field, mixed $value, string $operator): callable
    {
        return function (Builder $query) use ($field, $value, $operator) {
            if ($value && $operator === '=') {
                return $query->where($field, $value);
            }

            if ($value && $operator === 'like') {
                return $query->where($field, 'LIKE', '%' . $value . '%');
            }

            if ($value && (is_array($value) && $operator === 'in')) {
                return $query->whereIn($field, $value);
            }

            if ($value && (is_array($value) && $operator === 'between')) {
                return $query->whereBetween($field, $value);
            }

            if ($value && $operator === 'date') {
                return $query->whereDate($field, $value);
            }
        };
    }

    /**
     * Create relation models filter query
     *
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @return callable of query build
     */
    public function whereRelationFields(string $field, mixed $value, string $operator)
    {
        return function (Builder $query) use ($field, $value, $operator) {
            $data = explode('.', $field); // break string to array


            // if between 2 relations
            if (count($data) === 2) {
                $relation = $data[0];
                $relationField = $data[1];

                if ($this->isRelationExist($relation)) {
                    if ($operator === 'not_has') {
                        return $query->whereDoesntHave($relation);
                    }
                    return $query->whereHas($relation, $this->buildFiltersQuery($relationField, $value, $operator));
                }
            }

            // if between 3 relations
            if (count($data) === 3) {

                $relation = $data[0] . '.' . $data[1]; // ex: like relation book.creator ...
                $relationField = $data[2];
                if ($this->isRelationExist($relation)) {
                    if ($operator === 'not_has') {
                        return $query->whereDoesntHave($relation);
                    }
                    return $query->whereHas($relation, $this->buildFiltersQuery($relationField, $value, $operator));
                }
            }
        };
    }


    /**
     * Check if field is relation field
     *
     * @param string $field
     * @return bool
     */
    private function isRelationField(string $field): bool
    {
        return strpos($field, '.') !== false;
    }


    /**
     * Check if field is relation field
     *
     * @param string $field
     * @return bool
     */
    private function isRelationExist(string $relation): bool
    {
        return $this->model->has($relation)->count() > 0 ? true : false;
    }


    /**
     * @param array $filters
     * @return callable
     */
    public function filters($filters): callable
    {
        return function (Builder $query) use ($filters) {
            foreach ($filters as $filter) {
                ['field' => $field, 'value' => $value, 'operator' => $operator] = $filter;

                $query->where(
                    $this->isRelationField($field)
                        ? $this->whereRelationFields($field, $value, $operator)
                        : $this->buildFiltersQuery($field, $value, $operator)
                );
            }
        };
    }

    /**
     * sort data
     * @param array $sorts
     * @return callable
     */
    public function sorts($sorts): callable
    {
        return function (Builder $query) use ($sorts) {
            foreach ($sorts as $field => $sort) {
                $query->orderBy($field, $sort);
            }
        };
    }


    /**
     * build query before get data from database.
     * @param array $request
     * @return @return Model|Builder|QueryBuilder
     */
    public function buildQuery(?array $request = null): Model|Builder|QueryBuilder
    {
        $filters        = $request['filters'] ?? [];
        $select_fields  = $this->select_fields ?? [];
        $countRelations = $request['count_relations'] ?? [];
        $relations      = $request['relations'] ?? [];
        $sorts          = $request['sorts'] ?? [];

        return $this->model
            ->select($this->getSelectFields($select_fields))
            ->when(!empty($filters), $this->filters($filters))
            ->when(!empty($sorts), $this->sorts($sorts))
            ->when(!empty($countRelations), fn (Builder $query) => $query->withCount($countRelations))
            ->when(!empty($relations), fn (Builder $query) => $query->with($relations));
    }

    /**
     * return data with pagination
     * @param array $request
     * @return Collection|LengthAwarePaginator
     */
    public function paginate(array $request): Collection|LengthAwarePaginator
    {
        $limit = $request['limit'] ?? $this->limit;
        $query = $this->buildQuery($request);
        return $query->paginate($limit);
    }

    /**
     * return data with pagination and trash
     * @param array $request
     * @return Collection|LengthAwarePaginator
     */
    public function paginateOnlyTrash(array $request): Collection|LengthAwarePaginator
    {
        $limit = $request['limit'] ?? $this->limit;
        $query = $this->buildQuery($request)->onlyTrashed();
        return $query->paginate($limit);
    }

    /**
     * return one data by id
     * @param string|int $id
     * @param array $request
     * @return Model
     */
    public function getOneOrFail(string|int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * return many data
     * @param array $request
     * @return Collection
     */
    public function getMany(array $request): Collection
    {
        $limit = $request['limit'] ?? $this->limit;
        return !empty($limit)
            ? $this->buildQuery($request)->limit($limit)->get()
            : $this->buildQuery($request)->get();
    }

    /**
     * return many data by id
     * @param array $ids
     * @param array $request
     * @return Collection
     */
    public function getManyById(array $ids, array $request = []): Collection
    {
        return $this->buildQuery($request)->whereIn($this->getPrimaryKeyName(), $ids)->get();
    }

    /**
     * return one data from trash by id
     * @param string|int $id
     * @param array $request
     * @return Model
     */
    public function getOneOnlyTrash(string|int $id): Model
    {
        return $this->model->onlyTrashed()->findOrFail($id);
    }

    /**
     * return many data with trash
     * @param array $request
     * @return Collection
     */
    public function getManyOnlyTrash(array $request): Collection
    {
        $limit = $request['limit'] ?? $this->limit;
        return !empty($limit)
            ? $this->buildQuery($request)->limit($limit)->onlyTrashed()->get()
            : $this->buildQuery($request)->onlyTrashed()->get();
    }

    /**
     * create data
     * @param array $request
     * @return Model
     */
    public function createOne(array $request): Model
    {
        return $this->model->create($request);
    }

    /**
     * update data by id
     * @param Model $model
     * @param array $request
     * @return Model
     */
    public function updateOne(Model $model, array $request)
    {
        $model->update($request);
        return $model;
    }

    /**
     * destroy data by id
     * @param string|int $id
     * @return Model
     */
    public function deleteOne(string|int $id): Model
    {
        $model = $this->getOneOrFail($id);
        $model->delete();
        return $model;
    }

    /**
     * destroy many data by id
     * @param array $ids
     * @return bool
     */
    public function deleteMany(array $ids): bool
    {
        return $this->model->whereIn($this->getPrimaryKeyName(), $ids)->delete();
    }

    /**
     * force destroy data by id
     * @param string|int $id
     * @return bool
     */
    public function forceDeleteOne(string|int $id): bool
    {
        return $this->getOneOrFail($id)->forceDelete();
    }

    /**
     * force destroy many data by ids
     * @param array $ids
     * @return bool
     */
    public function forceDeleteMany(array $ids): bool
    {
        return $this->model->whereIn($this->getPrimaryKeyName(), $ids)->forceDelete();
    }

    /**
     * restore one data from trash
     * @param string|int $id
     * @return bool
     */
    public function restoreOne(string|int $id): bool
    {
        return $this->getOneOnlyTrash($id)->restore();
    }

    /**
     * restore many data by ids
     * @param array $ids
     * @return bool
     */
    public function restoreMany(array $ids): bool
    {
        return $this->model->whereIn($this->getPrimaryKeyName(), $ids)->onlyTrashed()->restore();
    }
}
