<?php

namespace Lokal\Butler\Manager;

use Lokal\Butler\Builder;

abstract class AbstractBreadCrumb extends Builder
{
    abstract public function setBreadCrumbs(): array;

    public function getCrumb(?string $key)
    {
        if(array_key_exists($key, $this->data())) {
            return $this->data()[$key];
        }

        return '';
    }

    protected function build(): array
    {
        return $this->setBreadCrumbs();
    }

    protected function validate($item)
    {
        return true;
    }
}