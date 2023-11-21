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

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Division\Models\Division;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\Division\Filament\Resources\DivisionResource;

class ViewDivision extends ViewRecord
{
    protected static string $resource = DivisionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('code'),
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
                        TextEntry::make('header')
                            ->columnSpanFull()
                            ->view('filament.infolists.entries.html'),
                        TextEntry::make('footer')
                            ->columnSpanFull()
                            ->view('filament.infolists.entries.html'),
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
