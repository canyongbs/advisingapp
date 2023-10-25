<?php

namespace Assist\Campaign\Filament\Resources\CampaignResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DateTimePicker;
use Assist\Campaign\Filament\Resources\CampaignResource;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    public function form(Form $form): Form
    {
        /** @var User $user */
        $user = auth()->user();

        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Select::make('caseload_id')
                    ->label('Caseload')
                    ->translateLabel()
                    ->options($user->caseloads()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                DateTimePicker::make('execute_at')
                    ->label('When should the campaign actions be executed?')
                    ->required(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
