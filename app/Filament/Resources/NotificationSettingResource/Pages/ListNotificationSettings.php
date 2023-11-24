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

<<<<<<<< HEAD:app-modules/engagement/src/Filament/Resources/EmailTemplateResource/Pages/ListEmailTemplates.php
namespace Assist\Engagement\Filament\Resources\EmailTemplateResource\Pages;
========
namespace App\Filament\Resources\NotificationSettingResource\Pages;
>>>>>>>> develop:app/Filament/Resources/NotificationSettingResource/Pages/ListNotificationSettings.php

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Columns\OpenSearch\TextColumn;
<<<<<<<< HEAD:app-modules/engagement/src/Filament/Resources/EmailTemplateResource/Pages/ListEmailTemplates.php
use Assist\Engagement\Filament\Resources\EmailTemplateResource;
========
use App\Filament\Resources\NotificationSettingResource;
>>>>>>>> develop:app/Filament/Resources/NotificationSettingResource/Pages/ListNotificationSettings.php

class ListNotificationSettings extends ListRecords
{
    protected static string $resource = NotificationSettingResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
<<<<<<<< HEAD:app-modules/engagement/src/Filament/Resources/EmailTemplateResource/Pages/ListEmailTemplates.php
                TextColumn::make('description'),
========
>>>>>>>> develop:app/Filament/Resources/NotificationSettingResource/Pages/ListNotificationSettings.php
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
