<?php

namespace Lokal\Butler\Repositories\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

abstract class AbstractBaseCriteria implements CriteriaInterface
{
    public function __construct(
        public array $query,
        public array $field = [],
        public ?string $search = null
    ) {}

    abstract public function apply($model, RepositoryInterface $repository);
}
