<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Campaign\Filament\Resources\CampaignResource\Pages;

use App\Models\User;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use AdvisingApp\Campaign\Models\Campaign;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Actions\CreateActionsForCampaign;
use AdvisingApp\Campaign\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use AdvisingApp\Campaign\DataTransferObjects\CampaignActionCreationData;
use AdvisingApp\Campaign\DataTransferObjects\CampaignActionsCreationData;

class CreateCampaign extends CreateRecord
{
    use HasWizard;

    protected static string $resource = CampaignResource::class;

    protected function getSteps(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Step::make('Campaign Details')
                ->schema([
                    TextInput::make('name')
                        ->autocomplete(false)
                        ->required(),
                    // TODO Add feature flag
                    Select::make('caseload_id')
                        ->label('Population Segment')
                        ->translateLabel()
                        ->options($user->caseloads()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ]),
            Step::make('Define Journey')
                ->schema([
                    Builder::make('actions')
                        ->label(fn () => new HtmlString('<span class="text-xl">Journey Steps</span>'))
                        ->addActionLabel('Add a new Journey Step')
                        ->minItems(1)
                        ->blocks(CampaignActionType::blocks()),
                ]),
            Step::make('Review Campaign')
                ->schema([
                    Toggle::make('enabled')
                        ->default(true)
                        ->helperText('Toggle this off to set your campaign to a draft state.'),
                    ViewField::make('step-summary')
                        ->view('filament.forms.components.campaigns.step-summary'),
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var Model $model */
        $model = static::getModel();

        /** @var Campaign $campaign */
        $campaign = $model::query()->create($data);

        resolve(CreateActionsForCampaign::class)->from(
            $campaign,
            CampaignActionsCreationData::from([
                'actions' => CampaignActionCreationData::collection($data['actions']),
            ])
        );

        return $campaign;
    }
}
