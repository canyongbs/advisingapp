<?php

namespace Assist\Form\Filament\Actions;

use Assist\Form\Models\FormRequest;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Assist\Form\Enums\FormRequestDeliveryMethod;
use Filament\Resources\Pages\ManageRelatedRecords;

class RequestForm extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->steps([
            Step::make('Form')
                ->schema([
                    Select::make('form_id')
                        ->relationship('form', 'name', fn (Builder $query) => $query->where('is_authenticated', true))
                        ->searchable()
                        ->preload()
                        ->model(FormRequest::class)
                        ->helperText('Forms must have authentication enabled to be requested, to verify the identity of the respondent.'),
                ]),
            Step::make('Notification')
                ->schema([
                    Select::make('method')
                        ->label('How would you like to send this request?')
                        ->options(FormRequestDeliveryMethod::class)
                        ->default(FormRequestDeliveryMethod::Email->value)
                        ->selectablePlaceholder(false)
                        ->live(),
                    Textarea::make('note')
                        ->columnSpanFull(),
                ]),
        ]);

        $this->action(function (array $data, ManageRelatedRecords $livewire) {
            $livewire->getOwnerRecord()->formRequests()->create($data);

            Notification::make()
                ->title('Form request sent')
                ->success()
                ->send();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'requestFormSubmission';
    }
}
