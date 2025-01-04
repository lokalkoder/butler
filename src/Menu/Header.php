<?php

namespace Lokal\Butler\Menu;

use Lokal\Butler\Builder;

class Header extends Builder
{
    protected ?string $header;

    protected bool $has_header = false;

    protected function build(): array
    {
        return [
            'show' => $this->setPermission(),
            'header' => $this->has_header ? $this->header ?? str(class_basename(get_called_class()))->title()->toString() : false,
            'items' => $this->menuItems(),
        ];
    }

    /**
     * Validation rules
     *
     * @return bool
     */
    protected function validate($item)
    {
        return $this->setPermission();
    }

    protected function setPermission(): bool
    {
        return true;
    }

    protected function menuItems(): array
    {
        return [];
    }
}