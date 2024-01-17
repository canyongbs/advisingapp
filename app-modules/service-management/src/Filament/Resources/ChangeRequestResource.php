<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Carbon\CarbonInterface;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components\Section;
use App\Filament\Clusters\ServiceManagement;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use AdvisingApp\ServiceManagement\Models\ChangeRequest;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\EditChangeRequest;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\ListChangeRequests;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\CreateChangeRequest;

class ChangeRequestResource extends Resource
{
    protected static ?string $model = ChangeRequest::class;

    protected static ?string $navigationLabel = 'Change Management';

    protected static ?string $navigationIcon = 'heroicon-m-arrow-path-rounded-square';

    protected static ?int $navigationSort = 30;

    protected static ?string $breadcrumb = 'Change Management';

    protected static ?string $cluster = ServiceManagement::class;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Change Request Details')
                    ->schema([
                        TextEntry::make('title')
                            ->columnSpan(3),
                        TextEntry::make('description')
                            ->columnSpan(3),
                        TextEntry::make('type.name')
                            ->label('Type')
                            ->columnSpan(3),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->columnSpan(3),
                        TextEntry::make('reason')
                            ->label('Reason for change')
                            ->columnSpanFull(),
                        TextEntry::make('backout_strategy')
                            ->columnSpanFull(),
                        TextEntry::make('start_time')
                            ->dateTime()
                            ->columnSpan(2),
                        TextEntry::make('end_time')
                            ->dateTime()
                            ->columnSpan(2),
                        TextEntry::make('created_at')
                            ->label('Duration')
                            ->state(fn ($record) => $record->end_time->diffForHumans($record->start_time, CarbonInterface::DIFF_ABSOLUTE, true, 6))
                            ->columnSpan(2),
                    ])
                    ->columns(6),
                Section::make('Risk Management')
                    ->schema([
                        TextEntry::make('impact')
                            ->columnSpan(1),
                        TextEntry::make('likelihood')
                            ->columnSpan(1),
                        ViewEntry::make('risk_score')
                            ->view('filament.infolists.entries.change-request.risk-score')
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChangeRequests::route('/'),
            'create' => CreateChangeRequest::route('/create'),
            'edit' => EditChangeRequest::route('/{record}/edit'),
        ];
    }
}
