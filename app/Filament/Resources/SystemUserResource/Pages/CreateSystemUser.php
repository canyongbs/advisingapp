<?php

namespace App\Filament\Resources\SystemUserResource\Pages;

use App\Filament\Resources\SystemUserResource;
use App\Models\SystemUser;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\HtmlString;

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
