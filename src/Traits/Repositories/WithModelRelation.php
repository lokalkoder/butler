<?php

namespace Lokal\Butler\Traits\Repositories;

use Illuminate\Database\Eloquent\Model;

trait WithModelRelation
{
    /**
     * @return string[]
     */
    protected function getRelated(mixed $text): array
    {
        $related = explode('.', $text);
        array_pop($related);

        return $related;
    }

    protected function attachRelation(mixed $text, Model $model): array
    {
        $relations = [];

        if (str_contains($text, '.')) {
            foreach ($this->getRelated($text) as $related) {
                if (! $model->relationLoaded($related)) {
                    array_push($relations, $related);
                }
            }
        }

        return $relations;
    }
}