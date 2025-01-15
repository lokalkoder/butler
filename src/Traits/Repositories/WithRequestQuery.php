<?php

namespace Lokal\Butler\Traits\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Lokal\Butler\Repositories\CommonPresenter;
use Lokal\Butler\Repositories\FilteringColumnsCriteria;
use Lokal\Butler\Repositories\SearchingColumnsCriteria;
use Lokal\Butler\Repositories\SelectionTransformer;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Exceptions\RepositoryException;

trait WithRequestQuery
{
    use WithModelRelation;

    protected $searchingCriteria;

    protected $filterCriteria;

    /**
     * @return $this
     */
    public function useSearchCriteria($criteria): static
    {
        $this->searchingCriteria = $criteria;

        return $this;
    }

    /**
     * @return $this
     *
     * @throws RepositoryException
     */
    public function useRequestSearching($request): static
    {
        $this->searchingCriteria($request);

        return $this;
    }

    /**
     * @return $this
     */
    public function useFilterCriteria($criteria): static
    {
        $this->filterCriteria = $criteria;

        return $this;
    }

    /**
     * @return $this
     *
     * @throws RepositoryException
     */
    public function useRequestFiltering($request): static
    {
        $this->filterCriteria($request);

        return $this;
    }

    /**
     * Searching resource base on parameter
     *
     * @response { success: bool - The request status, message: string - The response flash message, data: array - Return Data }
     *
     * @throws RepositoryException
     */
    public function querySearching($request)
    {
        $this->setOrderBy($request);

        $this->setFilteringQuery($request);

        [$search, $searchColumns] = $this->setSearchingQuery($request);

        return $request->get('paginate') ?
            $this->paginate($request->get('per_page')) :
            $this->all($searchColumns);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|mixed
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Exception
     */
    public function querySelectionList($request, string $field = 'name')
    {
        $this->setOrderBy($request);

        $filters = $this->getFilters($request);

        [$search, $searchColumns] = $this->setSearchingQuery($request);

        $mapping = $request->get('maps');

        $model = app($this->model());

        if ($searchColumns === ['*']) {
            $searchColumns = [
                $this->getDefaultColumn($model, $field),
                $model->getKeyName(),
            ];
        }

        if (filled($mapping)) {
            $this->setPresenter(new CommonPresenter(new SelectionTransformer($mapping)));

            foreach ($mapping as $map) {
                if (str_contains($map, '.')) {
                    $this->with($this->attachRelation($map, $model));
                }
            }

            $searchColumns = ['*'];
        }

        $data = filled($filters) ? $this->findWhere($filters, $searchColumns) : $this->all($searchColumns);

        return filled($mapping) ? $data['data'] : $data;
    }

    /**
     * @throws RepositoryException
     */
    public function queryCriteria(Request $request, $criteria, array $columns = ['*'])
    {
        $query = [];

        [$search, $searchColumns, $filters] = $this->queryRequest($request);

        $query = $this->searchingColumn($search, $searchColumns);

        $this->pushCriteria($this->getQueryCriteria($criteria, $query));

        if ($request->has('maps')) {
            $this->setPresenter(new CommonPresenter(new SelectionTransformer($request->get('maps'))));
        }

        $data = filled($filters) ? $this->findWhere($filters, $columns) : $this->all($columns);

        return $request->has('maps') ? $data['data'] : $data;
    }

    /**
     * @throws RepositoryException
     */
    protected function getQueryCriteria($criteria, array $where = [], array $field = [], ?string $search = null): CriteriaInterface
    {
        if (is_string($criteria)) {
            $criteria = new $criteria($where, $field, $search);

            if (! $criteria instanceof CriteriaInterface) {
                throw new RepositoryException('Class '.get_class($criteria).' must be an instance of Prettus\\Repository\\Contracts\\CriteriaInterface');
            }
        }

        return $criteria;
    }

    /**
     * Default Columns
     */
    protected function getDefaultColumn(Model $model, string $column): mixed
    {
        if (! Schema::hasColumn($model->getTable(), $column)) {
            $fillable = collect($model->getFillable());

            if ($fillable->isNotEmpty()) {
                return $fillable->first(fn ($item) => $item != $model->getKeyName() && Schema::getColumnType($model->getTable(), $item) == 'string');
            }

            $casts = collect($model->getCasts());

            if ($casts->isNotEmpty()) {
                return $casts->first(fn ($item) => $item != $model->getKeyName() && Schema::getColumnType($model->getTable(), $item) == 'string');
            }

        }

        return $column;
    }

    /**
     * Request Filter.
     */
    protected function getFilters(Request $request): mixed
    {
        $filters = $request->get('filters');

        $filters = is_string($filters) ? json_decode($filters, true) : $filters;

        return collect($filters)->mapWithKeys(function ($filter) {
            $field = current($filter);
            $condition = count($filter) == 2 ? '=' : next($filter);
            $value = end($filter);

            if ($value === 'null') {
                $value = null;
            }

            return [$field => [$field, $condition, $value]];
        })->toArray();
    }

    /**
     * Search Inputs
     */
    protected function getSearchInput(Request $request): mixed
    {
        return $request->get('search');
    }

    /**
     * Search Columns
     */
    protected function getSearchColumns(Request $request): mixed
    {
        $searchColumns = $request->get('search_columns');

        $searchColumns = is_string($searchColumns) ? json_decode($searchColumns, true) : $searchColumns;

        return $searchColumns ?? [];
    }

    /**
     * Search Columns Parameter
     */
    protected function searchingColumn($request): array
    {
        $search = $this->getSearchInput($request);

        $fields = $this->getSearchColumns($request);

        $where = [];

        if ((filled($search) && filled($fields))) {
            $where = Arr::map($fields, fn ($column) => [$column, 'LIKE', '%'.$search.'%']);
        }

        return [$search, $fields, $where];
    }

    protected function setOrderBy($request): void
    {
        $this->orderBy(DB::raw('ABS('.($request->get('sortBy') ?? 'id').')'), $request->get('sortType') ?? 'desc');
    }

    /**
     * @throws RepositoryException
     */
    protected function setSearchingQuery($request): array
    {
        [$search, $fields] = $this->searchingCriteria($request);

        return [$search, (filled($fields) ? $fields : '*')];
    }

    /**
     * @throws RepositoryException
     */
    protected function setFilteringQuery($request): mixed
    {
        $filters = $this->filterCriteria($request);

        return $filters;
    }

    /**
     * @throws RepositoryException
     */
    protected function searchingCriteria($request): array
    {
        [$search, $fields, $where] = $this->searchingColumn($request);

        if ($this->searchingCriteria) {
            $this->pushCriteria($this->getQueryCriteria($this->searchingCriteria, $where, $fields, $search));
        } else {
            if (filled($where)) {
                $this->pushCriteria(new SearchingColumnsCriteria($where));
            }
        }

        return [$search, $fields];
    }

    /**
     * @throws RepositoryException
     */
    protected function filterCriteria($request): mixed
    {
        $filters = $this->getFilters($request);

        if (filled($filters)) {
            if ($this->filterCriteria) {
                $this->pushCriteria($this->getQueryCriteria($this->filterCriteria, $filters));
            } else {
                $this->pushCriteria(new FilteringColumnsCriteria($filters));
            }
        }

        return $filters;
    }
}