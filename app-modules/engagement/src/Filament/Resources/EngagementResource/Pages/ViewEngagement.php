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

namespace AdvisingApp\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Engagement\Filament\Resources\EngagementResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;

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
                            ->color('primary')
                            ->url(function (Engagement $record) {
                                return UserResource::getUrl('view', ['record' => $record->user->id]);
                            }),
                        TextEntry::make('recipient')
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
                                    ->hidden(fn ($state): bool => blank($state))
                                    ->columnSpanFull(),
                                TextEntry::make('body')
                                    ->getStateUsing(fn (Engagement $engagement): HtmlString => $engagement->getBody())
                                    ->columnSpanFull(),
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
