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

namespace App\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\MeetingCenter\Managers\CalendarManager;
use App\Filament\Clusters\ProfileSettings;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @property Form $form
 */
class OutOfOffice extends ProfilePage
{
    protected static ?string $slug = 'out-of-office';

    protected static ?string $title = 'Out Of Office';

    protected static ?int $navigationSort = 90;

    /** @var array<string, mixed> $data */
    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ]),
            ]);
    }

}
