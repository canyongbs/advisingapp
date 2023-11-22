<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Division\Filament\Resources\DivisionResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use App\Models\NotificationSetting;
use Assist\Division\Models\Division;
use App\Filament\Fields\TiptapEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\Division\Filament\Resources\DivisionResource;

class EditDivision extends EditRecord
{
    protected static string $resource = DivisionResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->unique(ignoreRecord: true),
                TextInput::make('code')
                    ->required()
                    ->string()
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->string()
                    ->columnSpanFull(),
                TiptapEditor::make('header')
                    ->string()
                    ->columnSpanFull(),
                TiptapEditor::make('footer')
                    ->string()
                    ->columnSpanFull(),
                Select::make('notification_setting_id')
                    ->label('Notification Setting')
                    ->options(NotificationSetting::pluck('name', 'id'))
                    ->searchable(),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);

        /** @var Division $division */
        $division = $this->getRecord();

        $data['notification_setting_id'] = $division->notificationSetting?->setting->id;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);

        /** @var Division $division */
        $division = $this->getRecord();

        if ($data['notification_setting_id']) {
            $division->notificationSetting()->updateOrCreate([
                'related_to_id' => $division->id,
                'related_to_type' => $division->getMorphClass(),
            ], [
                'notification_setting_id' => $data['notification_setting_id'],
            ]);
        } else {
            $division->notificationSetting()->delete();
        }

        unset($data['notification_setting_id']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
