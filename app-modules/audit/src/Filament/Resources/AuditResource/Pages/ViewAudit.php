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

namespace Assist\Audit\Filament\Resources\AuditResource\Pages;

use Assist\Audit\Models\Audit;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\Audit\Filament\Resources\AuditResource;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('auditable_type')
                            ->label('Auditable'),
                        TextEntry::make('user.name')
                            ->label('Change Agent (User)')
                            ->placeholder('N/A'),
                        TextEntry::make('event')
                            ->label('Event'),
                        TextEntry::make('url')
                            ->label('URL'),
                        TextEntry::make('ip_address')
                            ->label('IP Address'),
                        TextEntry::make('user_agent')
                            ->label('User Agent'),
                        TextEntry::make('getModified')
                            ->label('Changes')
                            ->columnSpanFull()
                            ->state(function (Audit $record) {
                                return $record->getModified();
                            })
                            ->view('filament.infolists.entries.change-entry'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
