<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Audit\Filament\Resources\AuditResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
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
        return $table
            ->columns([
                IdColumn::make(),
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
        return [];
    }
}
