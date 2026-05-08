<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Forms\Components;

use App\Models\Scopes\WithoutAnyAdmin;
use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class UserSelect extends Select
{
    protected bool $filterAdmins = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->searchable(['name', 'email']);
    }

    public function withoutAdminFilter(): static
    {
        $this->filterAdmins = false;

        return $this;
    }

    public function relationship(string|Closure|null $name = null, string|Closure|null $titleAttribute = null, ?Closure $modifyQueryUsing = null, bool $ignoreRecord = false): static
    {
        return parent::relationship(
            $name,
            $titleAttribute ?? 'name',
            function (Builder $query, UserSelect $component) use ($modifyQueryUsing) {
                if ($component->shouldFilterAdmins()) {
                    $alreadySelected = [];

                    $record = $component->getRecord();
                    $relationshipName = $component->getRelationshipName();

                    if ($record && $record->exists && $relationshipName && ! str_contains($relationshipName, '.')) {
                        $qualifiedKey = $component->getRelationship()->getRelated()->getQualifiedKeyName();

                        $alreadySelected = $record->{$relationshipName}()
                            ->getQuery()
                            ->pluck($qualifiedKey)
                            ->map(fn ($variable) => (string) $variable)
                            ->toArray();
                    }

                    $state = $component->getState();
                    $stateSelected = array_filter(is_array($state) ? $state : [$state]);
                    $alreadySelected = array_values(array_unique(array_merge($alreadySelected, $stateSelected)));

                    $query->where(function (Builder $query) use ($alreadySelected) {
                        $query->tap(new WithoutAnyAdmin());

                        if (! empty($alreadySelected)) {
                            $query->orWhereIn('users.id', $alreadySelected);
                        }
                    });
                }

                if ($modifyQueryUsing) {
                    $modifyQueryUsing($query);
                }
            },
            $ignoreRecord,
        );
    }

    public function shouldFilterAdmins(): bool
    {
        return $this->filterAdmins && config('app.filter_admins_from_selection', true);
    }
}