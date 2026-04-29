<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\Pages;

use AdvisingApp\MeetingCenter\Enums\BookingGroupBookWith;
use AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\BookingGroupResource;
use AdvisingApp\Team\Models\Team;
use App\Filament\Forms\Components\DailyHoursRepeater;
use App\Filament\Forms\Components\DurationInput;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

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
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                            if (filled($get('slug'))) {
                                return;
                            }

                            $set('slug', Str::slug($state ?? ''));
                        })
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
                        ->options(BookingGroupBookWith::class)
                        ->default(BookingGroupBookWith::All)
                        ->live()
                        ->required(),
                    Select::make('users')
                        ->label('Users')
                        ->multiple()
                        ->relationship('users', 'name')
                        ->searchable()
                        ->preload()
                        ->live(),
                    Select::make('teams')
                        ->label('Teams')
                        ->multiple()
                        ->relationship('teams', 'name')
                        ->searchable()
                        ->preload()
                        ->live(),
                    Select::make('meeting_owner_id')
                        ->label('Meeting Owner')
                        ->options(fn (Get $get): array => User::query()
                            ->whereIn('id', $this->getEligibleMeetingOwnerIds($get))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->visible(fn (Get $get): bool => $this->isBookWithAll($get))
                        ->required(fn (Get $get): bool => $this->isBookWithAll($get))
                        ->rules([
                            function (Get $get): Closure {
                                return function (string $attribute, mixed $value, Closure $fail) use ($get) {
                                    if (! $this->isBookWithAll($get)) {
                                        return;
                                    }

                                    if (blank($value)) {
                                        return;
                                    }

                                    if (! in_array($value, $this->getEligibleMeetingOwnerIds($get), true)) {
                                        $fail('Meeting Owner must be selected from users in this booking group.');
                                    }
                                };
                            },
                            function (Get $get): Closure {
                                return function (string $attribute, mixed $value, Closure $fail) use ($get) {
                                    if (! $this->isBookWithAll($get) || blank($value)) {
                                        return;
                                    }

                                    $hasCalendar = User::query()
                                        ->whereKey($value)
                                        ->whereHas('calendar', fn (Builder $query) => $query->whereNotNull('oauth_token'))
                                        ->exists();

                                    if (! $hasCalendar) {
                                        $fail('Meeting Owner must have a connected calendar.');
                                    }
                                };
                            },
                        ]),
                    Callout::make('Some group members do not have a connected calendar and will be skipped during bookings.')
                        ->warning()
                        ->description(function (Get $get): HtmlString {
                            $selectedUserIds = array_values(array_filter(is_array($get('users')) ? $get('users') : []));
                            $selectedTeamIds = array_values(array_filter(is_array($get('teams')) ? $get('teams') : []));

                            $directUsers = User::query()
                                ->whereIn('id', $selectedUserIds)
                                ->whereDoesntHave('calendar', fn (Builder $query) => $query->whereNotNull('oauth_token'))
                                ->orderBy('name')
                                ->get();

                            $teamGroups = collect();

                            foreach (Team::query()->whereIn('id', $selectedTeamIds)->orderBy('name')->get() as $team) {
                                $disconnected = User::query()
                                    ->where('team_id', $team->id)
                                    ->whereDoesntHave('calendar', fn (Builder $query) => $query->whereNotNull('oauth_token'))
                                    ->orderBy('name')
                                    ->get();

                                if ($disconnected->isNotEmpty()) {
                                    $teamGroups->put($team->name, $disconnected);
                                }
                            }

                            return new HtmlString(view('meeting-center::filament.components.disconnected-calendar-members', [
                                'directUsers' => $directUsers,
                                'teamGroups' => $teamGroups,
                            ])->render());
                        })
                        ->visible(function (Get $get): bool {
                            $selectedUserIds = array_values(array_filter(is_array($get('users')) ? $get('users') : []));
                            $selectedTeamIds = array_values(array_filter(is_array($get('teams')) ? $get('teams') : []));

                            if (User::query()
                                ->whereIn('id', $selectedUserIds)
                                ->whereDoesntHave('calendar', fn (Builder $query) => $query->whereNotNull('oauth_token'))
                                ->exists()) {
                                return true;
                            }

                            return ! empty($selectedTeamIds) && User::query()
                                ->whereIn('team_id', $selectedTeamIds)
                                ->whereDoesntHave('calendar', fn (Builder $query) => $query->whereNotNull('oauth_token'))
                                ->exists();
                        })
                        ->columnSpanFull(),
                ]),
            Section::make('Availability')
                ->schema([
                    TextInput::make('slug')
                        ->label('URL Slug')
                        ->required()
                        ->alphaDash()
                        ->scopedUnique()
                        ->prefix(config('app.url') . '/group-booking/')
                        ->maxLength(255)
                        ->default(fn (Get $get) => Str::slug($get('name') ?? ''))
                        ->live(onBlur: true)
                        ->suffixAction(
                            Action::make('copyUrl')
                                ->icon('heroicon-o-clipboard')
                                ->tooltip('Copy URL')
                                ->alpineClickHandler(
                                    'const slug = $wire.get(\'data.slug\'); if (slug) { window.navigator.clipboard.writeText(' . Js::from(config('app.url') . '/group-booking/') . ' + slug); new FilamentNotification().title(\'URL copied to clipboard!\').success().send(); }'
                                )
                                ->visible(fn (Get $get): bool => filled($get('slug')))
                        )
                        ->columnSpanFull(),
                    DurationInput::make('default_appointment_duration', isRequired: true, hasDays: true)
                        ->label('Meeting Duration')
                        ->columnSpanFull(),
                    Toggle::make('is_default_appointment_buffer_enabled')
                        ->label('Buffer Time')
                        ->live()
                        ->columnSpanFull(),
                    DurationInput::make('default_appointment_buffer_before_duration', isRequired: true, hasDays: false)
                        ->label('Before')
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('is_default_appointment_buffer_enabled')),
                    DurationInput::make('default_appointment_buffer_after_duration', isRequired: true, hasDays: false)
                        ->label('After')
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('is_default_appointment_buffer_enabled')),
                    TextInput::make('minimum_booking_lead_time_hours')
                        ->label('Minimum Lead Time')
                        ->suffix('hours')
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(720)
                        ->integer()
                        ->columnSpanFull(),
                    TextInput::make('maximum_booking_lead_time_days')
                        ->label('Maximum Lead Time')
                        ->suffix('days')
                        ->default(0)
                        ->minValue(0)
                        ->integer()
                        ->columnSpanFull(),
                    DailyHoursRepeater::make('available_appointment_hours')
                        ->label('Days and Hours')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->toBookWithEnum($data['book_with'] ?? null) !== BookingGroupBookWith::All) {
            $data['meeting_owner_id'] = null;
        }

        $data['default_appointment_duration'] = DurationInput::mutateDataBeforeSave($data['default_appointment_duration']);

        if (array_key_exists('default_appointment_buffer_before_duration', $data)) {
            $data['default_appointment_buffer_before_duration'] = DurationInput::mutateDataBeforeSave($data['default_appointment_buffer_before_duration']);
        }

        if (array_key_exists('default_appointment_buffer_after_duration', $data)) {
            $data['default_appointment_buffer_after_duration'] = DurationInput::mutateDataBeforeSave($data['default_appointment_buffer_after_duration']);
        }

        $data['available_appointment_hours'] = DailyHoursRepeater::mutateDataBeforeSave($data['available_appointment_hours']);

        $data['minimum_booking_lead_time_hours'] = $data['minimum_booking_lead_time_hours'] ?? 0;
        $data['maximum_booking_lead_time_days'] = $data['maximum_booking_lead_time_days'] ?? 0;

        return $data;
    }

    /**
     * @return array<int, string>
     */
    protected function getEligibleMeetingOwnerIds(Get $get): array
    {
        $users = $get('users');
        $selectedUserIds = array_values(array_map(
            'strval',
            array_filter(is_array($users) ? $users : []),
        ));

        $teams = $get('teams');
        $selectedTeamIds = array_values(array_map(
            'strval',
            array_filter(is_array($teams) ? $teams : []),
        ));

        $teamUserIds = ! empty($selectedTeamIds)
            ? User::query()->whereIn('team_id', $selectedTeamIds)->pluck('id')->map(fn ($id): string => (string) $id)->all()
            : [];

        return array_values(array_unique([...$selectedUserIds, ...$teamUserIds]));
    }

    protected function isBookWithAll(Get $get): bool
    {
        return $this->toBookWithEnum($get('book_with')) === BookingGroupBookWith::All;
    }

    protected function toBookWithEnum(mixed $value): ?BookingGroupBookWith
    {
        if ($value instanceof BookingGroupBookWith) {
            return $value;
        }

        if (is_string($value)) {
            return BookingGroupBookWith::tryFrom($value);
        }

        return null;
    }
}
