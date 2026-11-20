<?php

namespace AdvisingApp\Prospect\Filament\Resources\Prospects\Schemas;

use App\Filament\Forms\Components\UserSelect;
use App\Models\User;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProspectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema(fn (Schema $schema): Schema => ProspectForm::configure($schema))
                    ->columnSpanFull(),
                Section::make('Record Details')
                    ->schema([
                        UserSelect::make('created_by_id')
                            ->label('Created By')
                            ->relationship('createdBy', 'name')
                            ->nullable()
                            ->exists(
                                table: (new User())->getTable(),
                                column: (new User())->getKeyName()
                            ),
                    ])
                    ->columns(2),
            ]);
    }
}
