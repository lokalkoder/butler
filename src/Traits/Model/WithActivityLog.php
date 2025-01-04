<?php

namespace Lokal\Butler\Traits\Model;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait WithActivityLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function ($eventName) {
                $eventName = str($eventName)->title()->toString();

                return class_basename(get_called_class()).' '.$eventName;
            })->useLogName('model');
    }
}