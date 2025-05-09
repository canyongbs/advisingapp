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

namespace App\Filament\Tables;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;

class UsersTable
{
    public function __invoke(Table $table): Table
    {
        return $table
            ->query(fn () => User::query())
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('name'),
                        QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->icon('heroicon-m-calendar'),
                        TextConstraint::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope'),
                        TextConstraint::make('phone_number')
                            ->icon('heroicon-m-phone'),
                    ])
                    ->constraintPickerColumns([
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ])
                    ->constraintPickerWidth('7xl'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ViewAction::make()
                    ->authorize('view')
                    ->url(fn (User $record) => UserResource::getUrl('view', ['record' => $record])),
            ]);
    }

    public static function configure(Table $table): Table
    {
        $instance = new self();

        return $instance($table);
    }
}
