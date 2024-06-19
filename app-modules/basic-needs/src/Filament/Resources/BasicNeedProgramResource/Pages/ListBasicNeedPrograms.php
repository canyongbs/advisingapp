<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource\Pages;

use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ListBasicNeedPrograms extends ListRecords
{
    protected static string $resource = BasicNeedProgramResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Program Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('basicNeedCategories.name')
                    ->label('Program Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_phone')
                    ->label('Contact Phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Location')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('availability')
                    ->label('Availability')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('eligibility_criteria')
                    ->label('Eligibility Criteria')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('application_process')
                    ->label('Application Process')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(config('project.datetime_format') ?? 'Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(config('project.datetime_format') ?? 'Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('basic_category_id')
                    ->label('Program Category')
                    ->relationship('basicNeedCategories', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
