<?php

namespace App\Filament\Tables;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use App\Filament\Resources\UserResource;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;

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
}
