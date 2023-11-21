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

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Prospect\Models\Prospect;
use App\Filament\Resources\UserResource;
use Assist\Engagement\Models\Engagement;
use Filament\Resources\Pages\ViewRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Engagement\Filament\Resources\EngagementResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class ViewEngagement extends ViewRecord
{
    protected static string $resource = EngagementResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Created By')
                            ->translateLabel()
                            ->color('primary')
                            ->url(function (Engagement $record) {
                                return UserResource::getUrl('view', ['record' => $record->user->id]);
                            }),
                        TextEntry::make('recipient')
                            ->translateLabel()
                            ->color('primary')
                            ->state(function (Engagement $record): string {
                                /** @var Student|Prospect $recipient */
                                $recipient = $record->recipient;

                                return match ($recipient::class) {
                                    Student::class => "{$recipient->{Student::displayNameKey()}} (Student)",
                                    Prospect::class => "{$recipient->{Prospect::displayNameKey()}} (Prospect)",
                                };
                            })
                            ->url(function (Engagement $record) {
                                /** @var Student|Prospect $recipient */
                                $recipient = $record->recipient;

                                return match ($recipient::class) {
                                    Student::class => StudentResource::getUrl('view', ['record' => $recipient->sisid]),
                                    Prospect::class => ProspectResource::getUrl('view', ['record' => $recipient->id]),
                                };
                            }),
                        Fieldset::make('Content')
                            ->schema([
                                TextEntry::make('subject')
                                    ->translateLabel(),
                                TextEntry::make('body')
                                    ->translateLabel(),
                            ]),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->hidden(fn (Engagement $record) => $record->hasBeenDelivered() === true),
        ];
    }
}
