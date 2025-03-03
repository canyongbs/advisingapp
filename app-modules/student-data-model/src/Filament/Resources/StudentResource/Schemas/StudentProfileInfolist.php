<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Schemas;

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentAddress;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use App\Features\ProspectStudentRefactor;
use App\Infolists\Components\Subsection;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class StudentProfileInfolist
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
                                ->label('Preferred Name')
                                ->placeholder('-'),
                            TextEntry::make('address')
                                ->state(fn (Student $record): array => collect($record->addresses)
                                    ->map(fn (StudentAddress $address): string => $address->full . (filled($address->type) ? " ({$address->type})" : ''))
                                    ->all())
                                ->listWithLineBreaks()
                                ->visible(fn (?array $state): bool => ProspectStudentRefactor::active() && filled($state)),
                            TextEntry::make('otherEmailAddresses')
                                ->state(fn (Student $record): array => collect($record->additionalEmailAddresses)
                                    ->map(fn (StudentEmailAddress $email): string => $email->address . (filled($email->type) ? " ({$email->type})" : ''))
                                    ->all())
                                ->listWithLineBreaks()
                                ->visible(fn (?array $state): bool => ProspectStudentRefactor::active() && filled($state)),
                            TextEntry::make('otherPhoneNumbers')
                                ->state(fn (Student $record): array => collect($record->additionalPhoneNumbers)
                                    ->map(fn (StudentPhoneNumber $phone): string => $phone->number . (filled($phone->ext) ? " (ext. {$phone->ext})" : '') . (filled($phone->type) ? " ({$phone->type})" : ''))
                                    ->all())
                                ->listWithLineBreaks()
                                ->visible(fn (?array $state): bool => ProspectStudentRefactor::active() && filled($state)),
                            TextEntry::make('phone')
                                ->hidden(ProspectStudentRefactor::active())
                                ->placeholder('-'),
                            TextEntry::make('email_2')
                                ->label('Alternate Email')
                                ->hidden(ProspectStudentRefactor::active())
                                ->placeholder('-'),
                            TextEntry::make('full_address')
                                ->label('Address')
                                ->placeholder('-')
                                ->hidden(ProspectStudentRefactor::active()),
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
                    ->extraAttributes(['class' => 'fi-section-has-subsections'])
                    ->headerActions([
                        Action::make('edit')
                            ->url(fn (): string => StudentResource::getUrl('edit', ['record' => $infolist->getRecord()])),
                    ]),
            ]);
    }
}
