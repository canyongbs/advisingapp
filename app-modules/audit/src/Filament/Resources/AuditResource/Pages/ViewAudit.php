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

namespace AdvisingApp\Audit\Filament\Resources\AuditResource\Pages;

use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use AdvisingApp\Audit\Models\Audit;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Audit\Filament\Resources\AuditResource;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    protected static ?string $title = 'View System Administration';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('auditable_type')
                            ->label('Auditable')
                            ->formatStateUsing(fn ($state) => Str::title($state)),
                        TextEntry::make('change_agent_name')
                            ->label('Change Agent (User)')
                            ->placeholder('System'),
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
                            ->view('filament.infolists.components.change-entry'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
