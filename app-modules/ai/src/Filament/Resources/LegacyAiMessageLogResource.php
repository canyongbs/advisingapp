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

namespace AdvisingApp\Ai\Filament\Resources;

use AdvisingApp\Ai\Filament\Exports\LegacyAiMessageExporter;
use AdvisingApp\Ai\Filament\Resources\LegacyAiMessageLogResource\Pages\ManageLegacyAiMessageLogs;
use AdvisingApp\Ai\Models\LegacyAiMessageLog;
use App\Filament\Clusters\UsageAuditing;
use App\Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LegacyAiMessageLogResource extends Resource
{
    protected static ?string $model = LegacyAiMessageLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationLabel = 'Assistant Utilization';

    protected static ?int $navigationSort = 30;

    protected static ?string $modelLabel = 'message log';

    protected static ?string $cluster = UsageAuditing::class;

    protected static ?string $slug = 'assistant-utilization';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name'),
                TextEntry::make('sent_at')
                    ->label('Sent')
                    ->dateTime(),
                TextEntry::make('ai_assistant_name')
                    ->label('Assistant')
                    ->default('N/A'),
                TextEntry::make('feature')
                    ->default('N/A'),
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
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('message')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('sent_at')
                    ->label('Sent')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(LegacyAiMessageExporter::class)
                        ->label('Export Records'),
                ]),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLegacyAiMessageLogs::route('/'),
        ];
    }
}
