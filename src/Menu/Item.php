<?php

namespace Lokal\Butler\Menu;

use Lokal\Butler\Builder;

class Item extends Builder
{
    public function __construct(
        protected string $route,
        protected string $label,
        protected ?string $icon = null,
        protected bool $show = true,
        protected array $subs = []
    ) {
        parent::__construct();
    }

    /**
     * @param callable|array $subs
     * @return $this
     */
    public function subs(callable|array $subs): Item
    {
        if (is_callable($subs)) {
            $subs = call_user_func($subs);

            if (!is_array($subs)) {
                $subs = [$subs];
            }
        }

        $this->subs = array_merge($this->subs, $subs);

        $this->iterable = $this->mapping();

        return $this;
    }

    /**
     * @param callable|bool $callback
     * @return Item
     */
    public function shown(callable|bool $callback): Item
    {
        $this->show = is_callable($callback) ? call_user_func($callback) : $callback;

        return $this;
    }

    /**
     * @return array
     */
    protected function build(): array
    {
        return [
            'show' => $this->show,
            'route' => $this->route,
            'label' => $this->label,
            'icon' => $this->icon,
            'subs' => $this->subs,
        ];
    }
}