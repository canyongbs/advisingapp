<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Resources\SystemUserResource\Pages;

use App\Filament\Resources\SystemUserResource;
use App\Models\SystemUser;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditSystemUser extends EditRecord
{
    protected static string $resource = SystemUserResource::class;

    protected ?string $heading = 'Edit Programmatic (API) User';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->string(),
            TextInput::make('token')
                ->hint('Please copy the token, it will only be shown once.')
                ->disabled()
                ->dehydrated(false)
                ->visible(fn (?string $state) => filled($state)),
        ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var SystemUser $systemUser */
        $systemUser = $this->getRecord();

        if (! $systemUser->tokens()->where('name', 'api')->first()) {
            $token = str($systemUser->createToken('api', ['graphql-api'])->plainTextToken)
                ->after('|')
                ->toString();

            $data['token'] = $token;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Reset Token')
                ->action(function (SystemUser $record) {
                    $record->tokens()->where('name', 'api')->delete();

                    $token = str($record->createToken('api', ['graphql-api'])->plainTextToken)
                        ->after('|')
                        ->toString();

                    $this->data['token'] = $token;
                }),
            DeleteAction::make(),
        ];
    }
}
