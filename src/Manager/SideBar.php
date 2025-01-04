<?php

namespace Lokal\Butler\Manager;

use Lokal\Butler\Builder;
use Lokal\Butler\Menu\Header;

class SideBar extends Builder
{
    protected function build(): array
    {
        return $this->getMenus();
    }

    protected function getMenus(): array
    {
        return collect(config('locale.sidebar', []))->filter(function ($sidebar) {
            return new $sidebar instanceof Header;
        })->map(function ($sidebar) {
            return (new $sidebar)->data();
        })->toArray();
    }
}