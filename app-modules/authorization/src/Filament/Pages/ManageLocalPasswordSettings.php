<?php

namespace AdvisingApp\Authorization\Filament\Pages;

use AdvisingApp\Authorization\Settings\LocalPasswordSettings;
use App\Features\LocalPassword;
use App\Filament\Clusters\Authentication;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageLocalPasswordSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Local Passwords';

    protected static ?string $navigationGroup = 'Local Authentication';

    protected static ?string $cluster = Authentication::class;

    protected static string $settings = LocalPasswordSettings::class;

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return LocalPassword::active() && $user->isSuperAdmin();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('minPasswordLength')
                    ->label('Minimum Password Length')
                    ->numeric()
                    ->step(1)
                    ->minValue(0)
                    ->helperText('Recommended: ' . LocalPasswordSettings::DEFAULT_MIN_PASSWORD_LENGTH . ' characters'),
                TextInput::make('maxPasswordLength')
                    ->label('Maximum Password Length')
                    ->numeric()
                    ->step(1)
                    ->minValue(0)
                    ->helperText('Recommended: ' . LocalPasswordSettings::DEFAULT_MAX_PASSWORD_LENGTH . ' characters'),
                TextInput::make('minUppercaseLetters')
                    ->label('Minimum Uppercase Letters')
                    ->numeric()
                    ->step(1)
                    ->minValue(0)
                    ->helperText('Recommended: ' . LocalPasswordSettings::DEFAULT_MIN_UPPERCASE_LETTERS . ' uppercase letter(s)'),
                TextInput::make('minLowercaseLetters')
                    ->label('Minimum Lowercase Letters')
                    ->numeric()
                    ->minValue(0)
                    ->step(1)
                    ->helperText('Recommended: ' . LocalPasswordSettings::DEFAULT_MIN_LOWERCASE_LETTERS . ' lowercase letter(s)'),
                TextInput::make('minDigits')
                    ->label('Minimum Numeric Characters')
                    ->numeric()
                    ->minValue(0)
                    ->step(1)
                    ->helperText('Recommended: ' . LocalPasswordSettings::DEFAULT_MIN_DIGITS . ' digit(s)'),
                TextInput::make('minSpecialCharacters')
                    ->label('Minimum Special Characters')
                    ->numeric()
                    ->minValue(0)
                    ->step(1)
                    ->helperText('Recommended: ' . LocalPasswordSettings::DEFAULT_MIN_SPECIAL_CHARACTERS . ' special character(s)'),
                TextInput::make('numPreviousPasswords')
                    ->label('Number of Previous Passwords to Remember')
                    ->numeric()
                    ->minValue(0)
                    ->step(1)
                    ->helperText('Recommended: Remember last ' . LocalPasswordSettings::DEFAULT_NUM_PREVIOUS_PASSWORDS . ' passwords'),
                TextInput::make('maxPasswordAge')
                    ->label('Maximum Password Age (days)')
                    ->numeric()
                    ->minValue(0)
                    ->step(1),
                Toggle::make('blacklistCommonPasswords')
                    ->label('Blacklist Common Passwords'),
            ]);
    }
}
