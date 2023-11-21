<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Audit\Overrides\Concerns;

use ReflectionClass;
use Illuminate\Support\Facades\Event;
use OwenIt\Auditing\Events\AuditCustom;
use OwenIt\Auditing\Contracts\Auditable;

trait AttachOverrides
{
    public function attach($id, array $attributes = [], $touch = true)
    {
        /** @var Auditable $parentModel */
        $parentModel = $this->getParent();

        if (! $this->isAuditable($parentModel::class)) {
            parent::attach($id, $attributes, $touch);

            return;
        }

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

        if (! $this->isAuditable($parentModel::class)) {
            return parent::detach($ids, $touch);
        }

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

        if (! $this->isAuditable($parentModel::class)) {
            return parent::sync($ids, $detaching);
        }

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

    private function isAuditable(string $class)
    {
        $reflection = new ReflectionClass($class);

        return $reflection->implementsInterface(Auditable::class);
    }
}
