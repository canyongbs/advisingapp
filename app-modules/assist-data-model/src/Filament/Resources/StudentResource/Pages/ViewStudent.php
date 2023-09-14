<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\Notifications\Actions\SubscriptionToggle;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Notifications\Filament\Actions\SubscribeHeaderAction;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('first')
                            ->label('First Name'),
                        TextEntry::make('last')
                            ->label('Last Name'),
                        TextEntry::make('preferred')
                            ->label('Preferred Name')
                            ->default('N/A'),
                        TextEntry::make('otherid')
                            ->label('Other ID'),
                        TextEntry::make('email')
                            ->label('Email Address'),
                        TextEntry::make('sisid')
                            ->label('Student ID'),
                        TextEntry::make('mobile')
                            ->label('Mobile'),
                        TextEntry::make('address')
                            ->label('Address'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            SubscribeHeaderAction::make(),
        ];
    }
}
