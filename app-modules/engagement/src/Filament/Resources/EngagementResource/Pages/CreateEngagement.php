<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Forms\Form;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Filament\Resources\EngagementResource;
use Assist\Engagement\Actions\CreateDeliverablesForEngagement;

class CreateEngagement extends CreateRecord
{
    protected static string $resource = EngagementResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('subject')
                    ->autofocus()
                    ->translateLabel()
                    ->required()
                    ->placeholder(__('Subject')),
                // TODO Add validation to ensure that the description abides by sms standards
                Textarea::make('description')
                    ->translateLabel()
                    ->placeholder(__('Description'))
                    ->columnSpanFull(),
                MorphToSelect::make('recipient')
                    ->label('Recipient')
                    ->translateLabel()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->types([
                        MorphToSelect\Type::make(Student::class)
                            ->titleAttribute('full'),
                        MorphToSelect\Type::make(Prospect::class)
                            ->titleAttribute('full'),
                    ]),
                Fieldset::make('Send your engagement')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this engagement will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('deliver_at')
                            ->required()
                            ->visible(fn (callable $get) => $get('send_later')),
                    ]),
                // TODO Better validation error messages here, "You must select at least 1 delivery method"
                // TODO We might want to make this option first,
                // so we can better and more easily validate the contents of the message
                Select::make('delivery_methods')
                    ->label('How would you like to send this engagement?')
                    ->translateLabel()
                    ->options(EngagementDeliveryMethod::class)
                    ->multiple()
                    ->minItems(1)
                    ->validationAttribute('Delivery Methods')
                    ->helperText('You can select multiple delivery methods.'),
            ]);
    }

    public function afterCreate(): void
    {
        $createDeliverablesForEngagement = resolve(CreateDeliverablesForEngagement::class);

        $createDeliverablesForEngagement($this->record, $this->data['delivery_methods']);
    }
}
