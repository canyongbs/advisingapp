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

namespace AdvisingApp\MeetingCenter\Filament\Pages;

use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use App\Features\PersonalBookingPageFeature;
use App\Filament\Pages\ProfilePage;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

    public static function canAccess(): bool
    {
        return parent::canAccess() && PersonalBookingPageFeature::active();
    }

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
        $bookingPage = PersonalBookingPage::query()->whereBelongsTo($user)->first();

        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->belowContent(
                        $hasCalendar
                            ? null
                            : 'This feature is only available if your Google or Outlook calendar is connected.'
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
                    ]),
            ]);
    }

    public function mutateFormDataBeforeFill(array $data): array
    {
        $user = auth()->user();
        assert($user instanceof User);
        $bookingPage = PersonalBookingPage::query()->whereBelongsTo($user)->first();

        if ($bookingPage) {
            return [
                'is_enabled' => $bookingPage->is_enabled,
                'slug' => $bookingPage->slug,
                'default_appointment_duration' => $bookingPage->default_appointment_duration,
            ];
        }

        return [
            'is_enabled' => false,
            'slug' => Str::slug($user->name),
            'default_appointment_duration' => 30,
        ];
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = $record;
        assert($user instanceof User);

        $bookingPage = $user->personalBookingPage()->firstOrNew();
        $bookingPage->is_enabled = $data['is_enabled'] ?? false;
        $bookingPage->slug = $data['slug'] ?? Str::slug($user->name);
        $bookingPage->default_appointment_duration = $data['default_appointment_duration'] ?? 30;
        $bookingPage->save();

        return $record;
    }
}
