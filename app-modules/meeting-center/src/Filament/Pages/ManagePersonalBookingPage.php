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

namespace AdvisingApp\MeetingCenter\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use App\Features\PersonalBookingAvailabilityFeature;
use App\Filament\Forms\Components\DailyHoursRepeater;
use App\Filament\Forms\Components\DurationInput;
use App\Filament\Pages\ProfilePage;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManagePersonalBookingPage extends ProfilePage
{
    protected static ?string $slug = 'personal-booking-page';

    protected static ?string $title = 'Personal Booking Page';

    protected static ?int $navigationSort = 20;

    public function getHeaderActions(): array
    {
        $user = auth()->user();
        assert($user instanceof User);

        return [
            Action::make('view_booking_page')
                ->label('View Booking Page')
                ->icon('heroicon-o-eye')
                ->url(fn (): string => route('direct-booking.show', ['slug' => PersonalBookingPage::query()->whereBelongsTo($user)->first()->slug]))
                ->openUrlInNewTab()
                ->visible(fn (): bool => PersonalBookingPage::query()->whereBelongsTo($user)->first()->is_enabled ?? false),
        ];
    }

    public function form(Schema $schema): Schema
    {
        $user = auth()->user();
        assert($user instanceof User);
        $hasCalendar = Calendar::query()->whereBelongsTo($user)->exists();
        $hasHours = $this->userHasHoursConfigured($user);
        $bookingPage = PersonalBookingPage::query()->whereBelongsTo($user)->first();
        $hasCrmLicense = $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]);

        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->belowContent(
                        match (true) {
                            ! $hasCalendar => 'This feature is only available if your Google or Outlook calendar is connected.',
                            ! $hasHours => 'This feature requires you to configure your office hours or working hours first.',
                            default => null,
                        }
                    )
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Enable booking page')
                            ->disabled(! $hasCalendar)
                            ->live(),
                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->rules([
                                'alpha_dash',
                                Rule::unique(PersonalBookingPage::class, 'slug')->ignore($bookingPage?->id),
                            ])
                            ->prefix(config('app.url') . '/direct-booking/')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('is_enabled')),
                        Select::make('default_appointment_duration')
                            ->label('Default Appointment Duration')
                            ->required()
                            ->options([
                                15 => '15 minutes',
                                30 => '30 minutes',
                                60 => '1 hour',
                            ])
                            ->visible(fn (Get $get) => $get('is_enabled')),
                        Section::make('Working Hours')
                            ->visible($hasCrmLicense)
                            ->schema([
                                Toggle::make('working_hours_are_enabled')
                                    ->label('Set Working Hours')
                                    ->live()
                                    ->required()
                                    ->hint(fn (Get $get): string => $get('are_working_hours_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile')
                                    ->validationMessages([
                                        'accepted' => 'Working hours must be enabled when booking page is enabled.',
                                    ])
                                    ->rules([
                                        fn (Get $get): string => $get('is_enabled') ? 'accepted' : '',
                                        function (Get $get) {
                                            return function (string $attribute, mixed $value, Closure $fail) use ($get) {
                                                if (! $value) {
                                                    return;
                                                }

                                                /** @var array<int, array<string, mixed>> $workingHours */
                                                $workingHours = $get('working_hours');

                                                if (empty($workingHours)) {
                                                    $fail('At least one day must have working hours configured.');

                                                    return;
                                                }

                                                $hasAnyEnabledDay = collect($workingHours)
                                                    ->filter(fn ($day) => ($day['enabled'] ?? false) === true && ! empty($day['starts_at']) && ! empty($day['ends_at']))
                                                    ->isNotEmpty();

                                                if (! $hasAnyEnabledDay) {
                                                    $fail('At least one day must have working hours configured.');
                                                }
                                            };
                                        },
                                    ]),
                                Checkbox::make('are_working_hours_visible_on_profile')
                                    ->label('Show Working Hours on profile')
                                    ->visible(fn (Get $get) => $get('working_hours_are_enabled'))
                                    ->live(),
                                Section::make('Days')
                                    ->schema($this->getHoursForDays('working_hours'))
                                    ->visible(fn (Get $get) => $get('working_hours_are_enabled')),
                            ])
                            ->visible(fn (Get $get) => $get('is_enabled')),
                        Section::make('Office Hours')
                            ->visible($hasCrmLicense)
                            ->schema([
                                Toggle::make('office_hours_are_enabled')
                                    ->label('Enable Office Hours')
                                    ->live()
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, mixed $value, Closure $fail) use ($get) {
                                                if (! $value) {
                                                    return;
                                                }

                                                /** @var array<int, array<string, mixed>> $officeHours */
                                                $officeHours = $get('office_hours');

                                                if (empty($officeHours)) {
                                                    $fail('At least one day must have office hours configured.');

                                                    return;
                                                }

                                                $hasAnyEnabledDay = collect($officeHours)
                                                    ->filter(fn ($day) => ($day['enabled'] ?? false) === true && ! empty($day['starts_at']) && ! empty($day['ends_at']))
                                                    ->isNotEmpty();

                                                if (! $hasAnyEnabledDay) {
                                                    $fail('At least one day must have office hours configured.');
                                                }
                                            };
                                        },
                                    ]),
                                Checkbox::make('appointments_are_restricted_to_existing_students')
                                    ->label('Restrict appointments to existing students')
                                    ->visible(fn (Get $get) => $get('office_hours_are_enabled')),
                                Section::make('Days')
                                    ->schema($this->getHoursForDays('office_hours'))
                                    ->visible(fn (Get $get) => $get('office_hours_are_enabled')),
                            ])
                            ->visible(fn (Get $get) => $get('is_enabled')),
                        Section::make('Out of Office')
                            ->schema([
                                Grid::make()
                                    ->columns([
                                        'sm' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ])
                                    ->schema([
                                        Toggle::make('out_of_office_is_enabled')
                                            ->columnSpanFull()
                                            ->label('Enable Out of Office')
                                            ->live(),
                                        DateTimePicker::make('out_of_office_starts_at')
                                            ->columnSpan(1)
                                            ->label('Start')
                                            ->required()
                                            ->visible(fn (Get $get) => $get('out_of_office_is_enabled')),
                                        DateTimePicker::make('out_of_office_ends_at')
                                            ->columnSpan(1)
                                            ->label('End')
                                            ->required()
                                            ->visible(fn (Get $get) => $get('out_of_office_is_enabled')),
                                    ]),
                            ])->visible(fn (Get $get) => $get('is_enabled')),
                        Section::make('Availability')
                            ->schema([
                                Grid::make()
                                    ->columns([
                                        'sm' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ])
                                    ->schema([
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
                            ])
                            ->visible(fn (): bool => PersonalBookingAvailabilityFeature::active()),
                    ]),
            ]);
    }

    public function mutateFormDataBeforeFill(array $data): array
    {
        $user = auth()->user();
        assert($user instanceof User);
        $bookingPage = PersonalBookingPage::query()->whereBelongsTo($user)->first();
        $hasCalendar = Calendar::query()->whereBelongsTo($user)->exists();

        $defaultAvailableHours = [
            ['day' => 'monday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ['day' => 'tuesday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ['day' => 'wednesday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ['day' => 'thursday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ['day' => 'friday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ['day' => 'saturday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
            ['day' => 'sunday', 'is_enabled' => false, 'starts_at' => null, 'ends_at' => null],
        ];

        if ($bookingPage) {
            $bookingData = [
                'is_enabled' => $hasCalendar ? $bookingPage->is_enabled : false,
                'slug' => $bookingPage->slug,
                'default_appointment_duration' => $bookingPage->default_appointment_duration,
                'working_hours_are_enabled' => $user->working_hours_are_enabled,
                'are_working_hours_visible_on_profile' => $user->are_working_hours_visible_on_profile,
                'working_hours' => $user->working_hours,
                'office_hours_are_enabled' => $user->office_hours_are_enabled,
                'appointments_are_restricted_to_existing_students' => $user->appointments_are_restricted_to_existing_students,
                'office_hours' => $user->office_hours,
                'out_of_office_is_enabled' => $user->out_of_office_is_enabled,
                'out_of_office_starts_at' => $user->out_of_office_starts_at,
                'out_of_office_ends_at' => $user->out_of_office_ends_at,
            ];

            if (PersonalBookingAvailabilityFeature::active()) {
                $bookingData = [
                    ...$bookingData,
                    'is_default_appointment_buffer_enabled' => $bookingPage->is_default_appointment_buffer_enabled,
                    'default_appointment_buffer_before_duration' => DurationInput::mutateDataBeforeFill($bookingPage->default_appointment_buffer_before_duration ?? 0, hasDays: false),
                    'default_appointment_buffer_after_duration' => DurationInput::mutateDataBeforeFill($bookingPage->default_appointment_buffer_after_duration ?? 0, hasDays: false),
                    'available_appointment_hours' => $bookingPage->available_appointment_hours
                        ? DailyHoursRepeater::mutateDataBeforeFill($bookingPage->available_appointment_hours)
                        : $defaultAvailableHours,
                ];
            }

            return $bookingData;
        }

        $userData = [
            'is_enabled' => false,
            'slug' => Str::slug($user->name),
            'default_appointment_duration' => 30,
            'working_hours_are_enabled' => $user->working_hours_are_enabled,
            'are_working_hours_visible_on_profile' => $user->are_working_hours_visible_on_profile,
            'working_hours' => $user->working_hours,
            'office_hours_are_enabled' => $user->office_hours_are_enabled,
            'appointments_are_restricted_to_existing_students' => $user->appointments_are_restricted_to_existing_students,
            'office_hours' => $user->office_hours,
            'out_of_office_is_enabled' => $user->out_of_office_is_enabled,
            'out_of_office_starts_at' => $user->out_of_office_starts_at,
            'out_of_office_ends_at' => $user->out_of_office_ends_at,
        ];

        if (PersonalBookingAvailabilityFeature::active()) {
            $userData = [
                ...$userData,
                'is_default_appointment_buffer_enabled' => false,
                'default_appointment_buffer_before_duration' => DurationInput::mutateDataBeforeFill(0, hasDays: false),
                'default_appointment_buffer_after_duration' => DurationInput::mutateDataBeforeFill(0, hasDays: false),
                'available_appointment_hours' => $defaultAvailableHours,
            ];
        }

        return $userData;
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = $record;
        assert($user instanceof User);

        $bookingPage = $user->personalBookingPage()->firstOrNew();
        $bookingPage->is_enabled = $data['is_enabled'] ?? false;
        $bookingPage->slug = $data['slug'] ?? Str::slug($user->name);
        $bookingPage->default_appointment_duration = $data['default_appointment_duration'] ?? 30;

        if (PersonalBookingAvailabilityFeature::active()) {
            $bookingPage->is_default_appointment_buffer_enabled = $data['is_default_appointment_buffer_enabled'] ?? false;
            $bookingPage->default_appointment_buffer_before_duration = isset($data['default_appointment_buffer_before_duration']) ? DurationInput::mutateDataBeforeSave($data['default_appointment_buffer_before_duration']) : 0;
            $bookingPage->default_appointment_buffer_after_duration = isset($data['default_appointment_buffer_after_duration']) ? DurationInput::mutateDataBeforeSave($data['default_appointment_buffer_after_duration']) : 0;
            $bookingPage->available_appointment_hours = isset($data['available_appointment_hours']) ? DailyHoursRepeater::mutateDataBeforeSave($data['available_appointment_hours']) : null;
        }
        $bookingPage->save();

        $user->update([
            'working_hours_are_enabled' => $data['working_hours_are_enabled'] ?? false,
            'are_working_hours_visible_on_profile' => $data['are_working_hours_visible_on_profile'] ?? false,
            'working_hours' => $data['working_hours'] ?? null,
            'office_hours_are_enabled' => $data['office_hours_are_enabled'] ?? false,
            'appointments_are_restricted_to_existing_students' => $data['appointments_are_restricted_to_existing_students'] ?? false,
            'office_hours' => $data['office_hours'] ?? null,
            'out_of_office_is_enabled' => $data['out_of_office_is_enabled'] ?? false,
            'out_of_office_starts_at' => $data['out_of_office_starts_at'] ?? null,
            'out_of_office_ends_at' => $data['out_of_office_ends_at'] ?? null,
        ]);

        return $record;
    }

    protected function userHasHoursConfigured(User $user): bool
    {
        $hasOfficeHours = $user->office_hours_are_enabled && ! empty($user->office_hours);
        $hasWorkingHours = $user->working_hours_are_enabled && ! empty($user->working_hours);

        return $hasOfficeHours || $hasWorkingHours;
    }
}
