<?php

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
        return parent::infolist($infolist)
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
