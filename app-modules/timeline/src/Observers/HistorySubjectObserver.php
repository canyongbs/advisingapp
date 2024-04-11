<?php

namespace AdvisingApp\Timeline\Observers;

use AdvisingApp\Timeline\Models\Contracts\HasHistory;

class HistorySubjectObserver
{
    public function created(HasHistory $model): void
    {
        $model->processHistory(
            'created',
            collect(),
            collect($model->getAttributes())->map(fn ($value) => (string) $value),
        );
    }

    public function updated(HasHistory $model): void
    {
        $model->processHistory(
            'updated',
            collect($model->getOriginal())->intersectByKeys($model->getChanges()),
            collect($model->getChanges())
        );
    }
}
