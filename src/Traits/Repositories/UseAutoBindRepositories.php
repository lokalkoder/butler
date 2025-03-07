<?php

namespace Lokal\Butler\Traits\Repositories;

use Spatie\StructureDiscoverer\Discover;

trait UseAutoBindRepositories
{
    public function bindRepositoriesContract(string $path)
    {
        foreach(Discover::in($path)->classes()->get() as $concrete) {
            $contracts = class_implements($concrete);
            $abstract = end($contracts);

            if ((new \ReflectionClass($abstract))->implementsInterface(\Prettus\Repository\Contracts\RepositoryInterface::class)) {
                $this->app->singleton($abstract, $concrete);
            }
        };
    }
}