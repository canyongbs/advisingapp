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

namespace Assist\Assistant\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Assist\Assistant\Models\AssistantChatMessageLog;
use Assist\Assistant\Filament\Resources\AssistantChatMessageLogResource\Pages;

class AssistantChatMessageLogResource extends Resource
{
    protected static ?string $model = AssistantChatMessageLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationLabel = 'Personal Assistant Audit';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Personal Assistant Audit';

    protected static ?string $pluralLabel = 'Personal Assistant Audit';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name'),
                TextEntry::make('sent_at')
                    ->label('Sent')
                    ->dateTime(),
                TextEntry::make('message')
                    ->prose()
                    ->columnSpanFull(),
                CodeEntry::make('metadata')
                    ->columnSpanFull(),
                CodeEntry::make('request')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Sent')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAssistantChatMessageLogs::route('/'),
        ];
    }
}
