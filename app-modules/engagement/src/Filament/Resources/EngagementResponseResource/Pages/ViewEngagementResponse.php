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

namespace Assist\Engagement\Filament\Resources\EngagementResponseResource\Pages;

use Filament\Infolists\Infolist;
use Assist\Prospect\Models\Prospect;
use Filament\Resources\Pages\ViewRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Engagement\Filament\Resources\EngagementResponseResource;

class ViewEngagementResponse extends ViewRecord
{
    protected static string $resource = EngagementResponseResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('sender')
                            ->label('Sent By')
                            ->translateLabel()
                            ->color('primary')
                            ->state(function (EngagementResponse $record): string {
                                /** @var Student|Prospect $sender */
                                $sender = $record->sender;

                                return match ($sender::class) {
                                    Student::class => "{$sender->full} (Student)",
                                    Prospect::class => "{$sender->full} (Prospect)",
                                };
                            })
                            ->url(function (EngagementResponse $record) {
                                /** @var Student|Prospect $sender */
                                $sender = $record->sender;

                                return match ($sender::class) {
                                    Student::class => StudentResource::getUrl('view', ['record' => $sender->sisid]),
                                    Prospect::class => ProspectResource::getUrl('view', ['record' => $sender->id]),
                                };
                            }),
                        TextEntry::make('content')
                            ->translateLabel(),
                    ])
                    ->columns(),
            ]);
    }
}
