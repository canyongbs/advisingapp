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

namespace AdvisingApp\Application\Livewire;

use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Models\Application;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * @property-read Schema $form
 */
abstract class ApplicationFormManager extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    #[Locked]
    public Application $record;

    public function mount(): void
    {
        $this->authorizeEdit();

        $this->form->fill($this->record->attributesToArray());
    }

    public function hydrate(): void
    {
        $this->authorizeEdit();
    }

    public function save(): void
    {
        $this->authorizeEdit();

        DB::transaction(function (): void {
            $data = $this->form->getState(afterValidate: function (): void {
                $this->beforeSave();
            });

            $record = $this->handleRecordUpdate($this->record, $data);
            assert($record instanceof Application);
            $this->record = $record;

            $this->form->model($record)->fill($record->attributesToArray());

            $this->afterSave();
        });

        Notification::make()
            ->title('Saved')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('application::livewire.application-form-manager');
    }

    public function formActions(Schema $schema): Schema
    {
        return $schema->model($this->record)->components([
            Actions::make($this->getFormActions()),
        ]);
    }

    /** @return array<Action> */
    protected function getFormActions(): array
    {
        return [Action::make('save')->label('Save')->submit('save')];
    }

    protected function authorizeEdit(): void
    {
        abort_unless(ApplicationResource::canAccess(), 403);
        abort_if($this->record->isArchived(), 403);

        ApplicationResource::authorizeEdit($this->record);
    }

    /**
     * @param array<string, mixed> $data
     */
    abstract protected function handleRecordUpdate(Model $record, array $data): Model;

    protected function beforeSave(): void {}

    protected function afterSave(): void {}
}
