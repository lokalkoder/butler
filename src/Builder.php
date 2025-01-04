<?php

namespace Lokal\Butler;

class Builder
{
    protected array $iterable;

    /**
     * Initiate class constructor
     */
    public function __construct()
    {
        $this->iterable = $this->mapping();
    }

    /**
     * Get data as array
     */
    public function data(): array
    {
        return $this->iterable;
    }

    /**
     * Validate data build
     *
     * @return array
     */
    protected function mapping()
    {
        return array_filter($this->build(), fn ($item) => $this->validate($item));
    }

    /**
     * Validation rules
     *
     * @return true
     */
    protected function validate($item)
    {
        return true;
    }

    /**
     * Define builder builds.
     *
     * @return array
     */
    protected function build(): array {
        return [];
    }
}