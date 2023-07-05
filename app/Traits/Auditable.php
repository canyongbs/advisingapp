<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::audit('audit:created', $model);
        });

        static::updated(function (Model $model) {
            $model->attributes = array_merge($model->getChanges(), ['id' => $model->id]);

            self::audit('audit:updated', $model);
        });

        static::deleted(function (Model $model) {
            self::audit('audit:deleted', $model);
        });
    }

    protected static function audit($description, $model)
    {
        AuditLog::create([
            'description'  => $description,
            'subject_id'   => $model->id ?? null,
            'subject_type' => sprintf('%s#%s', get_class($model), $model->id) ?? null,
            'user_id'      => auth()->id() ?? null,
            'properties'   => $model ?? null,
            'host'         => request()->ip() ?? null,
        ]);
    }
}
