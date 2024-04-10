<?php

namespace AdvisingApp\Timeline\Observers;

use Illuminate\Database\Eloquent\Model;

class HistorySubjectObserver
{
    public function created(Model $model): void
    {
        $model->processHistory(
            'created',
            collect(),
            collect($model->getAttributes())->map(fn ($value) => (string) $value),
        );
    }

    public function updated(Model $model): void
    {
        $model->processHistory(
            'updated',
            collect($model->getOriginal())->intersectByKeys($model->getChanges()),
            collect($model->getChanges())
        );
    }
}
