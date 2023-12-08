<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\AssistDataModel\Filament\Resources\StudentResource;
use AdvisingApp\Notifications\Filament\Actions\SubscribeHeaderAction;

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
