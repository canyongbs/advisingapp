<?php

namespace Assist\Campaign\Filament\Resources\CampaignResource\Pages;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;
use Assist\Campaign\Actions\CreateActionsForCampaign;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Campaign\Filament\Resources\CampaignResource;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Assist\Campaign\DataTransferObjects\CampaignActionCreationData;
use Assist\Campaign\DataTransferObjects\CampaignActionsCreationData;

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
                    Builder::make('actions')
                        ->blocks([
                            Builder\Block::make('bulk_engagement')
                                ->label('Bulk Engagement')
                                ->schema([
                                    Select::make('delivery_methods')
                                        ->reactive()
                                        ->label('How would you like to send this engagement?')
                                        ->options(EngagementDeliveryMethod::class)
                                        ->multiple()
                                        ->minItems(1)
                                        ->validationAttribute('Delivery Method')
                                        ->required(),
                                    TextInput::make('subject')
                                        ->required()
                                        ->placeholder(__('Subject'))
                                        ->hidden(fn (callable $get) => collect($get('delivery_methods'))->doesntContain(EngagementDeliveryMethod::Email->value))
                                        ->helperText('The subject will only be used for the email delivery method.'),
                                    Textarea::make('body')
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
                                ]),
                        ]),
                ]),
            Step::make('Schedule your campaign')
                ->schema([
                    DateTimePicker::make('execute_at')
                        ->label('When should the campaign actions be executed?')
                        ->required()
                        ->closeOnDateSelection(),
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $campaign = static::getModel()::create($data);

        resolve(CreateActionsForCampaign::class)->from(
            $campaign,
            CampaignActionsCreationData::from([
                'actions' => CampaignActionCreationData::collection($data['actions']),
            ])
        );

        return $campaign;
    }
}
