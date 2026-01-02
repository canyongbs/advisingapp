<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Application\Livewire;

use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionsChecklistItem;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * @property-read Schema $form
 */
class ApplicationSubmissionsChecklist extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    /**
     * @var array<string, string|null>
     */
    public ?array $data = [];

    public ApplicationSubmission $applicationSubmission;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('')
                ->placeholder('Add checklist item')
                ->maxLength(255),
        ])->statePath('data');
    }

    public function addChecklistItem(): void
    {
        $this->validate(
            rules: [
                'data.title' => ['required', 'string', 'max:255'],
            ],
            messages: [
                'data.title.required' => 'Please enter a checklist item.',
            ]
        );

        $formData = $this->form->getState();

        $this->applicationSubmission->checklistItems()->create([
            'title' => $formData['title'],
            'is_checked' => false,
            'created_by' => auth()->id(),
        ]);

        $this->form->fill();
    }

    /**
     * @return Collection<int, ApplicationSubmissionsChecklistItem>
     */
    public function loadChecklistItems(): ?Collection
    {
        return $this->applicationSubmission
            ->checklistItems()
            ->orderBy('created_at')
            ->get();
    }

    public function toggleItem(string $itemId): void
    {
        $item = $this->applicationSubmission->checklistItems()->where('id', $itemId)->first();

        if ($item instanceof ApplicationSubmissionsChecklistItem) {
            $isNowChecked = ! $item->is_checked;

            $item->update([
                'is_checked' => $isNowChecked,
                'completed_by' => $isNowChecked ? auth()->id() : null,
                'completed_date' => $isNowChecked ? now() : null,
            ]);
        }
    }

    public function deleteItem(string $itemId): void
    {
        $this->applicationSubmission->checklistItems()->find($itemId)?->delete();
    }

    public function render(): View
    {
        $checklistItems = $this->loadChecklistItems();

        return view('application::livewire.application-submissions-checklist', [
            'checklistItems' => $checklistItems,
        ]);
    }
}
