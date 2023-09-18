<?php

namespace Assist\Consent\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

class ConsentAgreementResource extends Resource
{
    protected static ?string $model = ConsentAgreement::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?int $navigationSort = 17;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('type')
                    ->readonly()
                    ->helperText('This field is not editable.'),
                TextInput::make('title')
                    ->required(),
                Fieldset::make('Content')
                    ->schema([
                        Textarea::make('description')
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                        Textarea::make('body')
                            ->required()
                            ->rows(5)
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type'),
                TextColumn::make('title'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateActions([]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsentAgreements::route('/'),
            // Creating consent agreements would currently require a code change
            // 'create' => Pages\CreateConsentAgreement::route('/create'),
            'edit' => Pages\EditConsentAgreement::route('/{record}/edit'),
        ];
    }
}
