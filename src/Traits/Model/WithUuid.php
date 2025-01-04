<?php

namespace Lokal\Butler\Traits\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait WithUuid
{
    public static function bootWithUuid(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public static function findByUuid(string $uuid): ?Model
    {
        return static::where('uuid', $uuid)->first();
    }
}