<?php

namespace Assist\Campaign\Filament\Resources\CampaignResource\Pages;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Assist\Campaign\Enums\CampaignActionType;
use Filament\Forms\Components\DateTimePicker;
use Assist\Campaign\Actions\CreateActionsForCampaign;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Campaign\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Assist\Campaign\DataTransferObjects\CampaignActionCreationData;

class CreateCampaign extends CreateRecord
{
    use HasWizard;

    protected static string $resource = CampaignResource::class;

    protected function getSteps(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Step::make('Setup your campaign')
                ->schema([
                    TextInput::make('name')
                        ->required(),
                    Select::make('caseload_id')
                        ->label('Caseload')
                        ->translateLabel()
                        ->options($user->caseloads()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ]),
            Step::make('Create your actions')
                ->schema([
                    Select::make('actions')
                        ->options(CampaignActionType::class)
                        ->multiple()
                        ->selectablePlaceholder(false)
                        ->required(),
                ]),
            Step::make('Define your campaign data')
                ->schema([
                    ...$this->actionSections(),
                ]),
            Step::make('Schedule your campaign')
                ->schema([
                    DateTimePicker::make('execute_at')
                        ->label('When should the campaign actions be executed?')
                        ->required(),
                ]),
        ];
    }

    protected function actionSections(): array
    {
        return [
            Section::make('Bulk Engagements')
                ->schema([
                    // TODO Re-use schema from Engagement creation
                    Select::make('actions_data.bulk_engagement.delivery_methods')
                        ->reactive()
                        ->label('How would you like to send this engagement?')
                        ->options(EngagementDeliveryMethod::class)
                        ->multiple()
                        ->minItems(1)
                        ->validationAttribute('Delivery Method')
                        ->required(),
                    TextInput::make('actions_data.bulk_engagement.subject')
                        ->required()
                        ->placeholder(__('Subject'))
                        ->hidden(fn (callable $get) => collect($get('actions_data')['bulk_engagement']['delivery_methods'])->doesntContain(EngagementDeliveryMethod::Email->value))
                        ->helperText('The subject will only be used for the email delivery method.'),
                    Textarea::make('actions_data.bulk_engagement.body')
                        ->placeholder(__('Body'))
                        ->required()
                        ->maxLength(function (callable $get) {
                            if (collect($get('delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                                return 320;
                            }

                            return 65535;
                        })
                        ->helperText(function (callable $get) {
                            if (collect($get('delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                                return 'The body of your message can be up to 320 characters long.';
                            }

                            return 'The body of your message can be up to 65,535 characters long.';
                        }),
                ])
                ->visible(fn (callable $get) => collect($get('actions'))->contains('bulk_engagement')),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $campaign = static::getModel()::create($data);

        resolve(CreateActionsForCampaign::class)->from(
            $campaign,
            CampaignActionCreationData::from([
                'actions' => $data['actions'],
                'actionsData' => $data['actions_data'],
            ])
        );

        return $campaign;
    }
}
