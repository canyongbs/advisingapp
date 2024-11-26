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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Prospect\Concerns\ProspectHolisticViewPage;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Notification\Filament\Actions\SubscribeHeaderAction;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions\ConvertToStudent;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions\ProspectTagAction;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions\DisassociateStudent;

class ViewProspect extends ViewRecord
{
    use ProspectHolisticViewPage;

    protected static string $resource = ProspectResource::class;

    // TODO: Automatically set from Filament
    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Demographics')
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('First Name'),
                        TextEntry::make('last_name')
                            ->label('Last Name'),
                        TextEntry::make(Prospect::displayNameKey())
                            ->label('Full Name'),
                        TextEntry::make('preferred')
                            ->label('Preferred Name'),
                        TextEntry::make('birthdate')
                            ->label('Birthdate'),
                        TextEntry::make('hsgrad')
                            ->label('High School Grad'),
                    ])
                    ->columns(2),
                Section::make('Contact Information')
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('email_2')
                            ->label('Alternate Email'),
                        TextEntry::make('mobile')
                            ->label('Mobile'),
                        TextEntry::make('phone')
                            ->label('Phone'),
                        TextEntry::make('address')
                            ->label('Address'),
                        TextEntry::make('address_2')
                            ->label('Apartment/Unit Number'),
                        TextEntry::make('address_3')
                            ->label('Additional Address'),
                        TextEntry::make('city')
                            ->label('City'),
                        TextEntry::make('state')
                            ->label('State'),
                        TextEntry::make('postal')
                            ->label('Postal'),
                    ])
                    ->columns(2),
                Section::make('Classification')
                    ->schema([
                        TextEntry::make('status.name')
                            ->label('Status'),
                        TextEntry::make('source.name')
                            ->label('Source'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('tags')
                            ->label('Tags')
                            ->formatStateUsing(function ($record) {
                                return $record->tags->pluck('name')->join(', ');
                            })
                            ->bulleted(),
                    ])
                    ->columns(2),
                Section::make('Engagement Restrictions')
                    ->schema([
                        IconEntry::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->boolean(),
                        IconEntry::make('email_bounce')
                            ->label('Email Bounce')
                            ->boolean(),
                    ])
                    ->columns(2),
                Section::make('Record Details')
                    ->schema([
                        TextEntry::make('createdBy.name')
                            ->label('Created By'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ProspectTagAction::make()->visible(fn () => auth()->user()?->can('prospect.tags.manage')),
            ConvertToStudent::make()->visible(fn (Prospect $record) => ! $record->student()->exists()),
            DisassociateStudent::make()->visible(fn (Prospect $record) => $record->student()->exists()),
            EditAction::make(),
            SubscribeHeaderAction::make(),
        ];
    }
}
