<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Clusters\ServiceManagementAdministration;
use AdvisingApp\ServiceManagement\Models\ChangeRequestStatus;
use AdvisingApp\ServiceManagement\Enums\SystemChangeRequestClassification;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource\Pages\ListChangeRequestStatuses;

class ChangeRequestStatusResource extends Resource
{
    protected static ?string $model = ChangeRequestStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 40;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('classification')
                            ->label('Classification'),
                    ])
                    ->columns(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string(),
                Select::make('classification')
                    ->searchable()
                    ->options(SystemChangeRequestClassification::class)
                    ->required()
                    ->enum(SystemChangeRequestClassification::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChangeRequestStatuses::route('/'),
        ];
    }
}
