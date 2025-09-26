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

namespace AdvisingApp\Authorization\Filament\Pages;

use AdvisingApp\Authorization\Settings\LocalPasswordSettings;
use App\Filament\Clusters\Authentication;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageLocalPasswordSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Local Passwords';

    protected static string | UnitEnum | null $navigationGroup = 'Local Authentication';

    protected static ?string $cluster = Authentication::class;

    protected static string $settings = LocalPasswordSettings::class;

    protected static ?int $navigationSort = 20;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
