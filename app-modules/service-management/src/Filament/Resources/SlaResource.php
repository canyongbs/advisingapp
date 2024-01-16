<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Filament\Clusters\ServiceManagement;
use AdvisingApp\ServiceManagement\Models\Sla;
use App\Filament\Fields\SecondsDurationInput;
use AdvisingApp\ServiceManagement\Filament\Resources\SlaResource\Pages;
use AdvisingApp\ServiceManagement\Filament\Resources\SlaResource\RelationManagers\ServiceRequestPrioritiesRelationManager;

class SlaResource extends Resource
{
    protected static ?string $model = Sla::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 40;

    protected static ?string $modelLabel = 'SLA';

    protected static ?string $pluralModelLabel = 'SLAs';

    protected static ?string $cluster = ServiceManagement::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                SecondsDurationInput::make('response_seconds')
                    ->label('Response time'),
                SecondsDurationInput::make('resolution_seconds')
                    ->label('Resolution time'),
                Textarea::make('terms')
                    ->label('Terms and conditions')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function getRelations(): array
    {
        return [
            ServiceRequestPrioritiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlas::route('/'),
            'create' => Pages\CreateSla::route('/create'),
            'edit' => Pages\EditSla::route('/{record}/edit'),
        ];
    }
}
