<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Schemas;

use Filament\Infolists\Infolist;
use App\Infolists\Components\Subsection;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class StudentProfileInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Profile Information')
                    ->schema([
                        Subsection::make([
                            TextEntry::make('email_2')
                                ->label('Alternate Email')
                                ->placeholder('-'),
                            TextEntry::make('phone')
                                ->placeholder('-'),
                            TextEntry::make('full_address')
                                ->label('Address')
                                ->placeholder('-'),
                        ]),
                        Subsection::make([
                            TextEntry::make('ethnicity')
                                ->placeholder('-'),
                            TextEntry::make('birthdate')
                                ->date()
                                ->placeholder('-'),
                            TextEntry::make('hsgrad')
                                ->label('High School Graduation')
                                ->placeholder('-'),
                        ]),
                        Subsection::make([
                            TextEntry::make('f_e_term')
                                ->label('First Term')
                                ->placeholder('-'),
                            TextEntry::make('mr_e_term')
                                ->label('Recent Term')
                                ->placeholder('-'),
                            TextEntry::make('holds')
                                ->label('SIS Holds')
                                ->placeholder('-'),
                        ]),
                    ])
                    ->extraAttributes(['class' => 'fi-section-has-subsections']),
            ]);
    }
}
