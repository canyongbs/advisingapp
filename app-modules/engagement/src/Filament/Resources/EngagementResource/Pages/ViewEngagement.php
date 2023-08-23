<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use App\Filament\Resources\UserResource;
use Assist\Engagement\Models\Engagement;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\Engagement\Filament\Resources\EngagementResource;

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
