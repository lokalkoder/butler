<?php

namespace Lokal\Butler\Repositories\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class BaseLookupSearchCriteria extends AbstractBaseCriteria implements CriteriaInterface
{
    public function apply($model, RepositoryInterface $repository): mixed
    {
        return $model->where(function ($query) {
            // Only for searching
            if ($this->search) {
                $query = $this->searchQuery($query, $this->search);
            }

            // Only for filter
            if ($this->query && ! filled($this->search)) {
                foreach ($this->query as $filter) {
                    if (in_array(current($filter), $this->exclusionList())) {
                        $query = $this->filterQuery($query, $filter);
                    } else {
                        $query->where(current($filter), next($filter), end($filter));
                    }
                }
            }
        })->when($this->includeTrashed(), function ($query) {
            $query->withTrashed();
        });
    }

    public function includeTrashed(): bool
    {
        return true;
    }

    /**
     * Search query parameter.
     */
    protected function searchQuery($query, $search): Builder
    {
        return $query;
    }

    /**
     * List of excluded field from filter.
     */
    protected function exclusionList(): array
    {
        return [];
    }

    /**
     * Filtered query parameter from  exclusion list
     */
    protected function filterQuery($query, $filter): Builder
    {
        return $query;
    }
}