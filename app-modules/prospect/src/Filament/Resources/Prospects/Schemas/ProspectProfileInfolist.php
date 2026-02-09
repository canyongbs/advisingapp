<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Prospect\Filament\Resources\Prospects\Schemas;

use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectAddress;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use App\Infolists\Components\Subsection;
use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ProspectProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->schema([
                        Subsection::make([
                            TextEntry::make('tags.name')
                                ->label('Tags')
                                ->badge()
                                ->placeholder('-')
                                ->state(
                                    fn ($record) => $record->tags->sortBy('name')->pluck('name')->all()
                                ),
                            TextEntry::make('preferred')
                                ->label('Preferred Name')
                                ->placeholder('-'),
                            TextEntry::make('addresses')
                                ->label(fn (?array $state): string => Str::plural('Address', count($state ?? [])))
                                ->state(fn (Prospect $record): array => collect($record->addresses)
                                    ->map(fn (ProspectAddress $address): string => $address->full . (filled($address->type) ? " ({$address->type})" : ''))
                                    ->all())
                                ->listWithLineBreaks()
                                ->visible(fn (?array $state): bool => filled($state)),
                            TextEntry::make('primaryEmailAddress')
                                ->label('Primary Email Address')
                                ->state(fn (Prospect $record): View => view('student-data-model::components.filament.resources.educatables.view-educatable.email-address-detail', ['emailAddress' => $record->primaryEmailAddress]))
                                ->visible(fn (Prospect $record): bool => filled($record->primaryEmailAddress)),
                            TextEntry::make('additionalEmailAddresses')
                                ->label(fn (?array $state): string => Str::plural('Other email address', count($state ?? [])))
                                ->state(fn (Prospect $record): array => array_map(
                                    fn (ProspectEmailAddress $emailAddress): View => view('student-data-model::components.filament.resources.educatables.view-educatable.email-address-detail', ['emailAddress' => $emailAddress]),
                                    $record->additionalEmailAddresses->all(),
                                ))
                                ->listWithLineBreaks()
                                ->visible(fn (Prospect $record): bool => filled($record->additionalEmailAddresses)),
                            TextEntry::make('primaryPhoneNumber')
                                ->label('Primary Phone Number')
                                ->state(fn (Prospect $record): View => view('student-data-model::components.filament.resources.educatables.view-educatable.phone-number-detail', ['phoneNumber' => $record->primaryPhoneNumber]))
                                ->visible(fn (Prospect $record): bool => filled($record->primaryPhoneNumber)),
                            TextEntry::make('additionalPhoneNumbers')
                                ->label(fn (?array $state): string => Str::plural('Other phone number', count($state ?? [])))
                                ->state(fn (Prospect $record): array => array_map(
                                    fn (ProspectPhoneNumber $phoneNumber): View => view('student-data-model::components.filament.resources.educatables.view-educatable.phone-number-detail', ['phoneNumber' => $phoneNumber]),
                                    $record->additionalPhoneNumbers->all(),
                                ))
                                ->listWithLineBreaks()
                                ->visible(fn (?array $state): bool => filled($state)),
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
                            TextEntry::make('createdBy.name')
                                ->label('Created By'),
                        ]),
                    ])
                    ->extraAttributes(['class' => 'fi-section-has-subsections'])
                    ->headerActions([
                        Action::make('edit')
                            ->url(fn (): string => ProspectResource::getUrl('edit', ['record' => $schema->getRecord()]))
                            ->visible(auth()->user()->can('update', $schema->getRecord())),
                    ]),
            ]);
    }
}
