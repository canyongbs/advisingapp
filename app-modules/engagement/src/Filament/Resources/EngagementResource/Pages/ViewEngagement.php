<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Prospect\Models\Prospect;
use App\Filament\Resources\UserResource;
use Assist\Engagement\Models\Engagement;
use Filament\Resources\Pages\ViewRecord;
use Assist\AssistDataModel\Models\Student;
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
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID')
                    ->translateLabel(),
                TextEntry::make('user')
                    ->label('Created By')
                    ->translateLabel()
                    ->color('primary')
                    ->state(function (Engagement $record): string {
                        return $record->user->name;
                    })
                    ->url(function (Engagement $record) {
                        return UserResource::getUrl('view', ['record' => $record->user->id]);
                    }),
                TextEntry::make('recipient')
                    ->label('Recipient')
                    ->translateLabel()
                    ->color('primary')
                    ->state(function (Engagement $record): string {
                        /** @var Student|Prospect $recipient */
                        $recipient = $record->recipient;

                        return match ($recipient::class) {
                            Student::class => "{$recipient->full} (Student)",
                            Prospect::class => "{$recipient->full} (Prospect)",
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
                        TextEntry::make('description')
                            ->translateLabel(),
                    ]),
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
