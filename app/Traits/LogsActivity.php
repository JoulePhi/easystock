<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            static::logActivity($model, 'created');
        });

        static::updated(function ($model) {
            static::logActivity($model, 'updated');
        });

        static::deleted(function ($model) {
            static::logActivity($model, 'deleted');
        });
    }

    protected static function logActivity($model, $action)
    {
        $request = request();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => get_class($model),
            'subject_id' => $model->id,
            'action' => $action,
            'description' => static::getActivityDescription($model, $action),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }

    protected static function getActivityDescription($model, $action)
    {
        $modelName = class_basename($model);

        switch ($action) {
            case 'created':
                return "Created new {$modelName}";
            case 'updated':
                return "Updated {$modelName}";
            case 'deleted':
                return "Deleted {$modelName}";
            default:
                return "Performed {$action} on {$modelName}";
        }
    }
}
