<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Application\Livewire;

use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionsChecklistItem;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * @property-read Form $form
 */
class ApplicationSubmissionsChecklist extends Component implements HasForms
{
    use InteractsWithForms;

    /**
     * @var array<string, string|null>
     */
    public ?array $data = [];

    public ApplicationSubmission $applicationSubmission;

    public function form(Form $form): Form
    {
        return $form->schema([
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
