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

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Notifications\Filament\Actions\SubscribeHeaderAction;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    // TODO: Automatically set from Filament
    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('first')
                            ->label('First Name'),
                        TextEntry::make('last')
                            ->label('Last Name'),
                        TextEntry::make('full_name')
                            ->label('Full Name'),
                        TextEntry::make('preferred')
                            ->label('Preferred Name')
                            ->default('N/A'),
                        TextEntry::make('sisid')
                            ->label('Student ID'),
                        TextEntry::make('otherid')
                            ->label('Other ID'),
                        TextEntry::make('email')
                            ->label('Email Address'),
                        TextEntry::make('email_2')
                            ->label('Email Address 2')
                            ->default('N/A'),
                        TextEntry::make('mobile'),
                        IconEntry::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->boolean(),
                        IconEntry::make('email_bounce')
                            ->label('Email Bounce')
                            ->boolean(),
                        TextEntry::make('phone'),
                        TextEntry::make('address'),
                        TextEntry::make('address2')
                            ->label('Address 2')
                            ->default('N/A'),
                        TextEntry::make('address3')
                            ->label('Address 3')
                            ->default('N/A'),
                        TextEntry::make('city'),
                        TextEntry::make('state'),
                        TextEntry::make('postal'),
                        TextEntry::make('birthdate'),
                        TextEntry::make('hsgrad')
                            ->label('High School Graduation')
                            ->default('N/A'),
                        IconEntry::make('dual')
                            ->label('Dual')
                            ->boolean(),
                        IconEntry::make('ferpa')
                            ->label('FERPA')
                            ->boolean(),
                        TextEntry::make('dfw')
                            ->label('DFW'),
                        IconEntry::make('sap')
                            ->label('SAP')
                            ->boolean(),
                        TextEntry::make('holds'),
                        IconEntry::make('firstgen')
                            ->label('First Generation')
                            ->boolean(),
                        TextEntry::make('ethnicity'),
                        TextEntry::make('lastlsmlogin')
                            ->label('Last LMS Login')
                            ->default('N/A'),
                        TextEntry::make('f_e_term')
                            ->label('First Enrollment Term')
                            ->default('N/A'),
                        TextEntry::make('mr_e_term')
                            ->label('Most Recent Enrollment Term')
                            ->default('N/A'),
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
