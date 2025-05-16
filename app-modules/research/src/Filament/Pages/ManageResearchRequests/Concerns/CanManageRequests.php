<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Research\Filament\Pages\ManageResearchRequests\Concerns;

use AdvisingApp\Ai\Actions\CreateRequest;
use AdvisingApp\Ai\Enums\ResearchRequestShareTarget;
use AdvisingApp\Ai\Jobs\PrepareResearchRequestCloning;
use AdvisingApp\Ai\Jobs\PrepareResearchRequestEmailing;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Ai\Rules\RestrictSuperAdmin;
use AdvisingApp\Ai\Services\Contracts\AiServiceLifecycleHooks;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use AdvisingApp\Team\Models\Team;
use App\Models\Scopes\WithoutSuperAdmin;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;

trait CanManageRequests
{
    #[Locked]
    public ?ResearchRequest $request = null;

    #[Locked]
    public array $requestsWithoutAFolder = [];

    public function mount(): void
    {
        if (request()->request) {
            if (! Str::isUuid(request()->request)) {
                $this->dispatch('remove-request-param');
            } else {
                $researchRequest = ResearchRequest::where('id', request()->request)->where('user_id', auth()->id())->first()?->toArray();

                if ($researchRequest) {
                    $this->selectRequest($researchRequest);
                } else {
                    $this->dispatch('remove-request-param');
                }
            }
        }
    }

    public function mountCanManageRequests(): void
    {
        $this->requestsWithoutAFolder = $this->getRequestsWithoutAFolder();
    }

    public function getRequestsWithoutAFolder(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $user
            ->researchRequests()
            ->whereNotNull('title')
            ->doesntHave('folder')
            ->latest('updated_at')
            ->get()
            ->toArray();
    }

    public function loadFirstRequest(): void
    {
        $this->selectRequest(collect($this->requestsWithoutAFolder)->whereNull('assistant.archived_at')->first());
    }

    public function selectRequest(?array $request): void
    {
        if (! $request) {
            return;
        }

        $request = ResearchRequest::find($request['id']);

        if (
            $this->request &&
            blank($this->request->title)
        ) {
            $this->request->delete();
        }

        if (! $request->user()->is(auth()->user())) {
            abort(404);
        }

        $this->request = $request;
    }

    public function deleteRequestAction(): Action
    {
        return Action::make('deleteRequest')
            ->size(ActionSize::ExtraSmall)
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $request = auth()->user()->researchRequests()
                    ->find($arguments['request']);

                if (! $request) {
                    return;
                }

                $request->delete();

                $this->requestsWithoutAFolder = $this->getRequestsWithoutAFolder();
                $this->folders = $this->getFolders();
            })
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function editRequestAction(): Action
    {
        return Action::make('editRequest')
            ->modalSubmitActionLabel('Save')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->fillForm(fn (array $arguments) => [
                'title' => auth()->user()->researchRequests()
                    ->find($arguments['request'])
                    ?->title,
            ])
            ->form([
                TextInput::make('title')
                    ->label('Title')
                    ->autocomplete(false)
                    ->placeholder('Rename this request')
                    ->required(),
            ])
            ->action(function (array $arguments, array $data) {
                $request = auth()->user()->researchRequests()
                    ->find($arguments['request']);

                if (! $request) {
                    return;
                }

                $request->title = $data['title'];
                $request->save();

                $this->requestsWithoutAFolder = $this->getRequestsWithoutAFolder();
                $this->folders = $this->getFolders();
            })
            ->icon('heroicon-m-pencil')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }
}
