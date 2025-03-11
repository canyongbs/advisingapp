<?php

namespace AdvisingApp\MultifactorAuthentication\Filament\Pages;

use AdvisingApp\MultifactorAuthentication\Settings\LocalPasswordSettings;
use App\Features\LocalPassword;
use App\Filament\Clusters\Authentication;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;

use function Filament\Support\is_app_url;

use Throwable;

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
                    ->numeric()
                    ->step(1)
                    ->minValue(LocalPasswordSettings::MIN_PASSWORD_LENGTH)
                    ->label('Minimum Password Length'),
                TextInput::make('maxPasswordLength')
                    ->numeric()
                    ->step(1)
                    ->minValue(1)
                    ->maxValue(LocalPasswordSettings::MAX_PASSWORD_LENGTH)
                    ->label('Maximum Password Length'),
                TextInput::make('minUppercaseLetters')
                    ->numeric()
                    ->step(1)
                    ->minValue(LocalPasswordSettings::MIN_UPPERCASE_LETTERS)
                    ->default(LocalPasswordSettings::MIN_UPPERCASE_LETTERS)
                    ->label('Minimum Uppercase Letters'),
                TextInput::make('minLowercaseLetters')
                    ->numeric()
                    ->minValue(LocalPasswordSettings::MIN_LOWERCASE_LETTERS)
                    ->step(1)
                    ->default(LocalPasswordSettings::MIN_LOWERCASE_LETTERS)
                    ->label('Minimum Lowercase Letters'),
                TextInput::make('minDigits')
                    ->numeric()
                    ->minValue(LocalPasswordSettings::MIN_DIGITS)
                    ->step(1)
                    ->default(LocalPasswordSettings::MIN_DIGITS)
                    ->label('Minimum Numeric Characters'),
                TextInput::make('minSpecialCharacters')
                    ->numeric()
                    ->minValue(LocalPasswordSettings::MIN_SPECIAL_CHARACTERS)
                    ->step(1)
                    ->default(LocalPasswordSettings::MIN_SPECIAL_CHARACTERS)
                    ->label('Minimum Special Characters'),
                TextInput::make('numPreviousPasswords')
                    ->numeric()
                    ->minValue(LocalPasswordSettings::NUM_PREVIOUS_PASSWORDS)
                    ->step(1)
                    ->default(LocalPasswordSettings::NUM_PREVIOUS_PASSWORDS)
                    ->label('Number of Previous Passwords to Remember'),
                TextInput::make('maxPasswordAge')
                    ->numeric()
                    ->minValue(LocalPasswordSettings::MAX_PASSWORD_AGE)
                    ->step(1)
                    ->default(LocalPasswordSettings::MAX_PASSWORD_AGE)
                    ->label('Maximum Password Age (days)'),
                Toggle::make('blacklistCommonPasswords')
                    ->default(LocalPasswordSettings::BLACKLIST_COMMON_PASSWORDS)
                    ->label('Blacklist Common Passwords'),
            ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $settings = app(static::getSettings());

            $settings->fill($data);
            $settings->save();

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            if ($exception->shouldRollbackDatabaseTransaction()) {
                $this->rollBackDatabaseTransaction();
            } else {
                $this->commitDatabaseTransaction();
            }

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->rememberData();

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }
}
