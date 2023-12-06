<?php

namespace App\Filament\Resources\SystemUserResource\Pages;

use Filament\Forms\Form;
use App\Models\SystemUser;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SystemUserResource;

class CreateSystemUser extends CreateRecord
{
    protected static string $resource = SystemUserResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->string(),
        ]);
    }

    public function afterCreate(): void
    {
        /** @var SystemUser $systemUser */
        $systemUser = $this->getRecord();

        $token = str($systemUser->createToken('api')->plainTextToken)->after('|')->toString();

        session()->put('apiToken', $token);
    }
}
