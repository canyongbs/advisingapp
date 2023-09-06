<?php

namespace Assist\Engagement\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Assist\Engagement\Actions\CreateEngagementBatch;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Notifications\EngagementBatchStartedNotification;

class BulkEngagementAction
{
    public static function make(string $context)
    {
        return BulkAction::make('engage')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Send Bulk Engagement')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} {$context} to engage.")
            ->steps([
                Step::make('Choose your delivery methods')
                    ->description('Select email, sms, or both.')
                    ->schema([
                        Select::make('delivery_methods')
                            ->label('How would you like to send this engagement?')
                            ->translateLabel()
                            ->options(EngagementDeliveryMethod::class)
                            ->multiple()
                            ->minItems(1)
                            ->validationAttribute('Delivery Method')
                            ->required(),
                    ])
                    ->columns(2),
                Step::make('Engagement Details')
                    ->description("Add the details that will be sent to the selected {$context}")
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
                        // TODO Figure out if we want to enable this later
                        // Fieldset::make('Send your engagement')
                        //     ->schema([
                        //         Toggle::make('send_later')
                        //             ->reactive()
                        //             ->helperText('By default, this engagement will send as soon as it is created unless you schedule it to send later.'),
                        //         DateTimePicker::make('deliver_at')
                        //             ->required()
                        //             ->visible(fn (callable $get) => $get('send_later')),
                        //     ]),
                    ]),
            ])
            ->action(function (Collection $records, array $data) {
                auth()->user()->notify(new EngagementBatchStartedNotification());

                CreateEngagementBatch::dispatch(auth()->user(), $records, $data);
            })
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            // FIXME This is currently not working exactly as expected. Dan is taking a look and will report back
            ->modalCancelAction(
                fn ($action) => Action::make('cancel')
                    ->requiresConfirmation()
                    ->modalDescription(fn () => 'The message has not been sent, are you sure you wish to return to the list view?')
                    ->cancelParentActions()
                    ->close()
                    ->color('gray'),
            )
            ->deselectRecordsAfterCompletion();
    }
}
