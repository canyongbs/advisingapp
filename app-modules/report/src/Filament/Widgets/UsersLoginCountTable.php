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

namespace AdvisingApp\Report\Filament\Widgets;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class UsersLoginCountTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $heading = 'Users Login Count';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function () {
                    return User::with('loginsCount');
                }
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email'),
                TextColumn::make('sisid')
                    ->getStateUsing(function ($record) {
                        return $record->first_login_at ? 'Yes' : 'No';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Yes' => 'success',
                        'No' => 'danger',
                    })
                    ->label('Logged In'),
                TextColumn::make('first_login_at')
                    ->label('First Login')
                    ->date('m-d-Y'),
                TextColumn::make('last_logged_in_at')
                    ->label('Last Login')
                    ->date('m-d-Y'),
                TextColumn::make('loginsCount.count')
                    ->label('Total Logins')
                    ->default(0),
            ])
            ->filters([
                SelectFilter::make('User')
                    ->options([
                        'logged_in' => 'Has Logged In',
                        'never_logged_in' => 'Never Logged In',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'logged_in') {
                            $query->whereNotNull('first_login_at');
                        } elseif ($data['value'] === 'never_logged_in') {
                            $query->whereNull('first_login_at');
                        }
                    }),
                Filter::make('first_login_at')
                    ->form([
                        DatePicker::make('first_logged_in_from'),
                        DatePicker::make('first_logged_in_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['first_logged_in_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('first_login_at', '>=', $date),
                            )
                            ->when(
                                $data['first_logged_in_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('first_login_at', '<=', $date),
                            );
                    }),
                Filter::make('last_logged_in_at')
                    ->form([
                        DatePicker::make('last_logged_in_from'),
                        DatePicker::make('last_logged_in_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['last_logged_in_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_logged_in_at', '>=', $date),
                            )
                            ->when(
                                $data['last_logged_in_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('last_logged_in_at', '<=', $date),
                            );
                    }),
            ])
            ->filtersFormWidth(MaxWidth::Small)
            ->paginated([10]);
    }
}
