<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Clusters\ServiceManagementAdministration;
use AdvisingApp\ServiceManagement\Models\ChangeRequestType;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource\Pages\ListChangeRequestTypes;

class ChangeRequestTypeResource extends Resource
{
    protected static ?string $model = ChangeRequestType::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    protected static ?int $navigationSort = 30;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        RepeatableEntry::make('userApprovers')
                            ->schema([
                                TextEntry::make('name')
                                    ->hiddenLabel()
                                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record]))
                                    ->color('primary'),
                            ]),
                    ])
                    ->columns(1),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string(),
                Select::make('userApprovers')
                    ->label('User approvers')
                    ->relationship('userApprovers', 'name')
                    ->preload()
                    ->multiple()
                    ->exists((new User())->getTable(), 'id'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChangeRequestTypes::route('/'),
        ];
    }
}
