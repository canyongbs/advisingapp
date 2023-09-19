<?php

namespace Assist\Audit\Filament\Resources\AuditResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Assist\Audit\Actions\Finders\AuditableModels;
use Assist\Audit\Filament\Resources\AuditResource;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('auditable_type')
                    ->label('Auditable')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Change Agent (User)')
                    ->sortable(),
                TextColumn::make('event')
                    ->label('Event')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('change_agent_user')
                    ->label('Change Agent (User)')
                    ->options(fn (): array => User::query()->pluck('name', 'id')->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data) => $data['value'] ? $query->where('change_agent_type', 'user')->where('change_agent_id', $data['value']) : null),
                SelectFilter::make('auditable')
                    ->label('Auditable')
                    ->options(AuditableModels::all())
                    ->query(fn (Builder $query, array $data) => $data['value'] ? $query->where('auditable_type', $data['value']) : null),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
