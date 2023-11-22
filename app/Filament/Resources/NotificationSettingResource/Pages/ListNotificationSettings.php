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

namespace App\Filament\Resources\NotificationSettingResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Pages\EmailConfiguration;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Columns\OpenSearch\TextColumn;
use App\Filament\Resources\NotificationSettingResource;

class ListNotificationSettings extends ListRecords
{
    protected static string $resource = NotificationSettingResource::class;

    protected static ?string $navigationLabel = 'Notification Settings';

    protected static ?string $title = 'Notification Settings';

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public function getBreadcrumbs(): array
    {
        return [
            ...(new EmailConfiguration())->getBreadcrumbs(),
            $this::getUrl() => $this::getNavigationLabel(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
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

    // public static function getNavigationItems(array $urlParameters = []) : array
    // {
    //     ray(parent::getNavigationItems($urlParameters))
    //     return parent::getNavigationItems($urlParameters);
    // }

    public function getSubNavigation(): array
    {
        return (new EmailConfiguration())->getSubNavigation();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
