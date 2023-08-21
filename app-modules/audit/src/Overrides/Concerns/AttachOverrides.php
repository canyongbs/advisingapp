<?php

namespace Assist\Audit\Overrides\Concerns;

use Illuminate\Support\Facades\Event;
use OwenIt\Auditing\Events\AuditCustom;
use OwenIt\Auditing\Contracts\Auditable;

trait AttachOverrides
{
    public function attach($id, array $attributes = [], $touch = true)
    {
        /** @var Auditable $parentModel */
        $parentModel = $this->getParent();
        $relationName = $this->relationName;

        $parentModel->auditEvent = 'attach';
        $parentModel->isCustomEvent = true;
        $parentModel->auditCustomOld = [
            $relationName => $parentModel->{$relationName}()->get()->toArray(),
        ];

        parent::attach($id, $attributes, $touch);

        $parentModel->auditCustomNew = [
            $relationName => $parentModel->{$relationName}()->get()->toArray(),
        ];
        Event::dispatch(AuditCustom::class, [$parentModel]);
        $parentModel->isCustomEvent = false;
    }

    public function detach($ids = null, $touch = true)
    {
        /** @var Auditable $parentModel */
        $parentModel = $this->getParent();
        $relationName = $this->relationName;

        $parentModel->auditEvent = 'detach';
        $parentModel->isCustomEvent = true;
        $parentModel->auditCustomOld = [
            $relationName => $parentModel->{$relationName}()->get()->toArray(),
        ];

        $results = parent::detach($ids, $touch);

        $parentModel->auditCustomNew = [
            $relationName => $parentModel->{$relationName}()->get()->toArray(),
        ];
        Event::dispatch(AuditCustom::class, [$parentModel]);
        $parentModel->isCustomEvent = false;

        return empty($results) ? 0 : $results;
    }

    public function sync($ids, $detaching = true)
    {
        /** @var Auditable $parentModel */
        $parentModel = $this->getParent();
        $relationName = $this->relationName;

        $parentModel->auditEvent = 'sync';

        $parentModel->auditCustomOld = [
            $relationName => $parentModel->{$relationName}()->get()->toArray(),
        ];

        $changes = parent::sync($ids, $detaching);

        if (collect($changes)->flatten()->isEmpty()) {
            $parentModel->auditCustomOld = [];
            $parentModel->auditCustomNew = [];
        } else {
            $parentModel->auditCustomNew = [
                $relationName => $parentModel->{$relationName}()->get()->toArray(),
            ];
        }

        $parentModel->isCustomEvent = true;
        Event::dispatch(AuditCustom::class, [$parentModel]);
        $parentModel->isCustomEvent = false;

        return $changes;
    }
}
