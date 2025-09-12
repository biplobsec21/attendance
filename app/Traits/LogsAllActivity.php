<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

trait LogsAllActivity
{
    use LogsActivity;

    /**
     * Configure the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName(strtolower(class_basename($this)))
            ->setDescriptionForEvent(
                fn(string $eventName) =>
                class_basename($this) . " {$eventName} by " . (auth()->check() ? auth()->user()->name : 'System')
            );
    }

    /**
     * Relation to fetch all actions for this model.
     */
    public function actions()
    {
        return $this->hasMany(Activity::class, 'causer_id');
    }
}
