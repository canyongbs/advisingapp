<?php

namespace App\Filament\Resources\SystemUserResource\Pages;

use Filament\Forms\Form;
use App\Models\SystemUser;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SystemUserResource;

class EditSystemUser extends EditRecord
{
    protected static string $resource = SystemUserResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->string(),
            TextInput::make('token')
                ->hint('Please copy the token, it will only be shown once.')
                ->dehydrated(false)
                ->visible(fn (?string $state) => filled($state)),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['token'] = session()->pull('apiToken');

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Reset Token')
                ->action(function (SystemUser $record) {
                    $record->tokens()->where('name', 'api')->delete();

                    $token = str($record->createToken('api')->plainTextToken)->after('|')->toString();

                    $this->data['token'] = $token;
                }),
            DeleteAction::make(),
        ];
    }
}
