<?php

namespace AdvisingApp\Ai\Filament\Resources;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Clusters\UsageAuditing;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use AdvisingApp\Ai\Models\LegacyAiMessageLog;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Infolists\Components\CodeEntry;
use AdvisingApp\Ai\Filament\Exports\LegacyAiMessageExporter;
use AdvisingApp\Ai\Filament\Resources\LegacyAiMessageLogResource\Pages\ManageLegacyAiMessageLogs;

class LegacyAiMessageLogResource extends Resource
{
    protected static ?string $model = LegacyAiMessageLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationLabel = 'Personal Assistant';

    protected static ?int $navigationSort = 30;

    protected static ?string $modelLabel = 'message log';

    protected static ?string $cluster = UsageAuditing::class;

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
                        ->exporter(LegacyAiMessageExporter::class),
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
