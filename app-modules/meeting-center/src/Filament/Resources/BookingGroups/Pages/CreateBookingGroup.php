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

namespace AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\Pages;

use AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\BookingGroupResource;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use App\Features\GroupBookingFeature;
use App\Filament\Forms\Components\DailyHoursRepeater;
use App\Filament\Forms\Components\DurationInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CreateBookingGroup extends CreateRecord
{
    protected static string $resource = BookingGroupResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Booking Group Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->string()
                        ->maxLength(255)
                        ->label('Name'),
                    Textarea::make('description')
                        ->string()
                        ->maxLength(65535)
                        ->columnSpanFull()
                        ->label('Description'),
                ]),
            Section::make('Members')
                ->schema([
                    Select::make('book_with')
                        ->label('Book With')
                        ->options(['all' => 'All'])
                        ->default('all')
                        ->required()
                        ->visible(GroupBookingFeature::active()),
                    Select::make('users')
                        ->label('Users')
                        ->multiple()
                        ->relationship('users', 'name')
                        ->searchable()
                        ->preload(),
                    Select::make('teams')
                        ->label('Teams')
                        ->multiple()
                        ->relationship('teams', 'name')
                        ->searchable()
                        ->preload(),
                ]),
            Section::make('Availability')
                ->schema([
                    TextInput::make('slug')
                        ->label('URL Slug')
                        ->required()
                        ->rules([
                            'alpha_dash',
                            Rule::unique(BookingGroup::class, 'slug'),
                        ])
                        ->prefix(config('app.url') . '/group-booking/')
                        ->maxLength(255)
                        ->default(fn (Get $get) => Str::slug($get('name') ?? ''))
                        ->visible(GroupBookingFeature::active()),
                    DurationInput::make('default_appointment_duration', isRequired: true, hasDays: true)
                        ->label('Meeting Duration'),
                    Toggle::make('is_default_appointment_buffer_enabled')
                        ->label('Buffer Time')
                        ->live()
                        ->columnStart(1),
                    DurationInput::make('default_appointment_buffer_before_duration', isRequired: true, hasDays: false)
                        ->label('Before')
                        ->columnStart(1)
                        ->visible(fn (Get $get): bool => $get('is_default_appointment_buffer_enabled')),
                    DurationInput::make('default_appointment_buffer_after_duration', isRequired: true, hasDays: false)
                        ->label('After')
                        ->visible(fn (Get $get): bool => $get('is_default_appointment_buffer_enabled')),
                    DailyHoursRepeater::make('available_appointment_hours')
                        ->label('Days and Hours')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['default_appointment_duration'] = DurationInput::mutateDataBeforeSave($data['default_appointment_duration']);

        if (array_key_exists('default_appointment_buffer_before_duration', $data)) {
            $data['default_appointment_buffer_before_duration'] = DurationInput::mutateDataBeforeSave($data['default_appointment_buffer_before_duration']);
        }

        if (array_key_exists('default_appointment_buffer_after_duration', $data)) {
            $data['default_appointment_buffer_after_duration'] = DurationInput::mutateDataBeforeSave($data['default_appointment_buffer_after_duration']);
        }

        $data['available_appointment_hours'] = DailyHoursRepeater::mutateDataBeforeSave($data['available_appointment_hours']);

        return $data;
    }
}
