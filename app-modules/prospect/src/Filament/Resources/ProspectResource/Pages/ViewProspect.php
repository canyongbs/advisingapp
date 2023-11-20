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

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Prospect\Models\Prospect;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Notifications\Filament\Actions\SubscribeHeaderAction;

class ViewProspect extends ViewRecord
{
    protected static string $resource = ProspectResource::class;

    // TODO: Automatically set from Filament
    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->translateLabel(),
                        TextEntry::make('source.name')
                            ->label('Source')
                            ->translateLabel(),
                        TextEntry::make('first_name')
                            ->label('First Name')
                            ->translateLabel(),
                        TextEntry::make('last_name')
                            ->label('Last Name')
                            ->translateLabel(),
                        TextEntry::make(Prospect::displayNameKey())
                            ->label('Full Name')
                            ->translateLabel(),
                        TextEntry::make('preferred')
                            ->label('Preferred Name')
                            ->translateLabel(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->translateLabel(),
                        TextEntry::make('email')
                            ->label('Email')
                            ->translateLabel(),
                        TextEntry::make('email_2')
                            ->label('Email 2')
                            ->translateLabel(),
                        TextEntry::make('mobile')
                            ->label('Mobile')
                            ->translateLabel(),
                        TextEntry::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->translateLabel()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('email_bounce')
                            ->label('Email Bounce')
                            ->translateLabel()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('phone')
                            ->label('Phone')
                            ->translateLabel(),
                        TextEntry::make('address')
                            ->label('Address')
                            ->translateLabel(),
                        TextEntry::make('address_2')
                            ->label('Address 2')
                            ->translateLabel(),
                        TextEntry::make('birthdate')
                            ->label('Birthdate')
                            ->translateLabel(),
                        TextEntry::make('hsgrad')
                            ->label('High School Grad')
                            ->translateLabel(),
                        TextEntry::make('assignedTo.name')
                            ->label('Assigned To')
                            ->translateLabel(),
                        TextEntry::make('createdBy.name')
                            ->label('Created By')
                            ->translateLabel(),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            SubscribeHeaderAction::make(),
        ];
    }
}
