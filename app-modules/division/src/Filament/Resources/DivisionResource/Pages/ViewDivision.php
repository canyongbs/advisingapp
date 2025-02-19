<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Division\Filament\Resources\DivisionResource\Pages;

use AdvisingApp\Division\Filament\Resources\DivisionResource;
use AdvisingApp\Division\Models\Division;
use App\Filament\Resources\NotificationSettingResource;
use App\Filament\Resources\UserResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDivision extends ViewRecord
{
    protected static string $resource = DivisionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('code'),
                                IconEntry::make('is_default')
                                    ->label('Default'),
                            ])
                            ->columns(3),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        Section::make()
                            ->schema([
                                TextEntry::make('createdBy.name')
                                    ->default('N/A')
                                    ->label('Created By')
                                    ->color(fn (Division $record) => $record->createdBy ? 'primary' : null)
                                    ->url(fn (Division $record) => $record->createdBy ? UserResource::getUrl('view', ['record' => $record->createdBy]) : null),
                                TextEntry::make('created_at')
                                    ->datetime(config('project.datetime_format') ?? 'Y-m-d H:i:s'),
                                TextEntry::make('lastUpdatedBy.name')
                                    ->default('N/A')
                                    ->label('Last Updated By')
                                    ->color(fn (Division $record) => $record->lastUpdatedBy ? 'primary' : null)
                                    ->url(fn (Division $record) => $record->lastUpdatedBy ? UserResource::getUrl('view', ['record' => $record->lastUpdatedBy]) : null),
                                TextEntry::make('updated_at')
                                    ->datetime(config('project.datetime_format') ?? 'Y-m-d H:i:s'),
                            ])
                            ->columns(),
                        TextEntry::make('notificationSetting.setting.name')
                            ->label('Notification Setting')
                            ->color(fn (Division $record) => $record->notificationSetting?->setting ? 'primary' : null)
                            ->url(fn (Division $record) => $record->notificationSetting?->setting ? NotificationSettingResource::getUrl('edit', ['record' => $record->notificationSetting->setting]) : null),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
