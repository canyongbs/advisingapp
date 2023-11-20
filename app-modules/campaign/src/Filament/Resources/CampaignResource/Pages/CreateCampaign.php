<?php

namespace Assist\Campaign\Filament\Resources\CampaignResource\Pages;

use App\Models\User;
use Illuminate\Support\HtmlString;
use Assist\Campaign\Models\Campaign;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Assist\Campaign\Filament\Blocks\TaskBlock;
use Assist\Campaign\Filament\Blocks\CareTeamBlock;
use Assist\Campaign\Actions\CreateActionsForCampaign;
use Assist\Campaign\Filament\Blocks\InteractionBlock;
use Assist\Campaign\Filament\Blocks\SubscriptionBlock;
use Assist\Campaign\Filament\Blocks\ProactiveAlertBlock;
use Assist\Campaign\Filament\Blocks\ServiceRequestBlock;
use Assist\Campaign\Filament\Resources\CampaignResource;
use Assist\Campaign\Filament\Blocks\EngagementBatchBlock;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Assist\Campaign\DataTransferObjects\CampaignActionCreationData;
use Assist\Campaign\DataTransferObjects\CampaignActionsCreationData;

class CreateCampaign extends CreateRecord
{
    use HasWizard;

    protected static string $resource = CampaignResource::class;

    public static function blocks(): array
    {
        return [
            EngagementBatchBlock::make(),
            ServiceRequestBlock::make(),
            ProactiveAlertBlock::make(),
            InteractionBlock::make(),
            CareTeamBlock::make(),
            TaskBlock::make(),
            SubscriptionBlock::make(),
        ];
    }

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
                    Select::make('caseload_id')
                        ->label('Caseload')
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
                        ->blocks(CreateCampaign::blocks()),
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
