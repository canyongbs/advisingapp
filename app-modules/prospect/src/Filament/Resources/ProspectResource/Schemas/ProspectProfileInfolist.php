<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Schemas;

use Filament\Infolists\Infolist;
use App\Infolists\Components\Subsection;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

class ProspectProfileInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Profile Information')
                    ->schema([
                        Subsection::make([
                            TextEntry::make('tags.name')
                                ->label('Tags')
                                ->badge()
                                ->placeholder('-'),
                            TextEntry::make('preferred')
                                ->label('Preferred Name'),
                            TextEntry::make('phone')
                                ->placeholder('-'),
                            TextEntry::make('email_2')
                                ->label('Alternate Email')
                                ->placeholder('-'),
                            TextEntry::make('full_address')
                                ->label('Address')
                                ->placeholder('-'),
                        ]),
                        Subsection::make([
                            TextEntry::make('status.name')
                                ->label('Status'),
                            TextEntry::make('source.name')
                                ->label('Source'),
                            TextEntry::make('description')
                                ->label('Description'),
                        ]),
                        Subsection::make([
                            IconEntry::make('sms_opt_out')
                                ->label('SMS Opt Out')
                                ->boolean(),
                            IconEntry::make('email_bounce')
                                ->label('Email Bounce')
                                ->boolean(),
                        ]),
                        Subsection::make([
                            TextEntry::make('createdBy.name')
                                ->label('Created By'),
                        ]),
                    ])
                    ->extraAttributes(['class' => 'fi-section-has-subsections']),
            ]);
    }
}
