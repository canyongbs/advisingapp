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

namespace AdvisingApp\Report\Filament\Forms\Components\LiveFilterBuilder;

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Report\Filament\Forms\Components\LiveFilterBuilder;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Component;

/**
 * The nested Livewire component behind {@see LiveFilterBuilder}.
 *
 * It renders the subject model's table + QueryBuilder constraints and binds its filter state
 * ($tableFilters) back to the parent field via a modelable $state property. Because it owns its
 * own table (and therefore its own action/modal stack), it can be embedded inside another action
 * modal without recursion — exactly like Filament's own TableSelect component.
 */
class LiveFilterBuilderComponent extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable {
        updatedTableFilters as baseUpdatedTableFilters;
    }

    #[Locked]
    public string $groupModel;

    /**
     * The bound filter state, shared with the parent field via `wire:model`.
     *
     * @var array<string, mixed> | null
     */
    #[Modelable]
    public ?array $state = null;

    public function mount(): void
    {
        if (filled($this->state)) {
            $this->tableFilters = $this->state;
        }
    }

    public function table(Table $table): Table
    {
        return GroupModel::from($this->groupModel)->table($table)
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public function updatedTableFilters(): void
    {
        $this->baseUpdatedTableFilters();

        $this->state = $this->tableFilters;
    }

    public function render(): string
    {
        return '{{ $this->table }}';
    }
}
