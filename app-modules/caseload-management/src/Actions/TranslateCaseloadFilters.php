<?php

namespace Assist\CaseloadManagement\Actions;

use function Livewire\trigger;

use Illuminate\Database\Eloquent\Builder;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\EditCaseload;

class TranslateCaseloadFilters
{
    public function handle(Caseload | string $caseload): Builder
    {
        ray('TranslateCaseloadFilters.handle()');

        // Create a fake Livewire component to replicate the table on the EditCaseload page.
        $page = app('livewire')->new(EditCaseload::class);

        ray('page', $page);

        if ($caseload instanceof Caseload) {
            $caseload = $caseload->getKey();
        }

        ray('caseload', $caseload);

        // Mount the fake Livewire component with the desired caseload.
        trigger('mount', $page, [$caseload], null, null);

        ray('after trigger');

        // Extract the filtered table query from the fake Livewire component,
        // which already respects both dynamic and static populations.
        return $page->getFilteredTableQuery();
    }
}
