<?php

namespace AdvisingApp\Organization\Filament\Resources\Organizations\Tables;

use AdvisingApp\Organization\Models\Organization;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->description(
                        fn (Organization $record): ?string => filled($record->description)
                        ? Str::limit($record->description, 50)
                        : null
                    )
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email'),
                TextColumn::make('phone_number'),
                TextColumn::make('website'),
                TextColumn::make('address')
                    ->description(function (Organization $record): ?HtmlString {
                        $lines = array_filter([
                            collect([$record->city, $record->state])->filter()->implode(', '),
                            collect([$record->postalcode, $record->country])->filter()->implode(' '),
                        ]);

                        return filled($lines) ? new HtmlString(implode('<br>', $lines)) : null;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
