<?php

namespace Lokal\Butler\Repositories;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use Lokal\Butler\Traits\Repositories\WithModelRelation;

class SelectionTransformer extends TransformerAbstract
{
    use WithModelRelation;

    public function __construct(public array $mapping) {}

    /**
     * Transform the User entity.
     *
     *
     * @return array
     */
    public function transform(Model $model)
    {
        $this->checkRelations($model);

        return [
            'id' => data_get($model, $this->mapping['id']),
            'text' => data_get($model, $this->mapping['text']),
        ];
    }

    /**
     * @return void
     */
    protected function checkRelations(Model $model)
    {
        $relations = array_merge(
            $this->attachRelation($this->mapping['id'], $model),
            $this->attachRelation($this->mapping['text'], $model)
        );

        if (! empty($relations)) {
            $model->load(array_unique($relations));
        }
    }
}