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

namespace App\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

/**
 * @property Schema $form
 */
class ProfileInformation extends ProfilePage
{
    protected static ?string $slug = 'profile-information';

    protected static ?string $title = 'Profile Information';

    protected static ?int $navigationSort = 10;

    public function form(Schema $schema): Schema
    {
        /** @var User $user */
        $user = auth()->user();
        $hasCrmLicense = $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]);

        return $schema
            ->components([
                Section::make('Public Profile')
                    ->visible($hasCrmLicense)
                    ->schema([
                        Toggle::make('has_enabled_public_profile')
                            ->label('Enable public profile')
                            ->live(),
                        TextInput::make('public_profile_slug')
                            ->label('Url')
                            ->visible(fn (Get $get) => $get('has_enabled_public_profile'))
                            //TODO: default doesn't work for some reason
                            ->afterStateHydrated(fn (TextInput $component, $state) => $component->state($state ?? str($user->name)->lower()->slug('')))
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->required()
                            //The id doesn't matter because we're just using it to generate a piece of a url
                            ->prefix(str(route('users.profile.view.public', ['user' => -1]))->beforeLast('/')->append('/'))
                            ->suffixAction(
                                Action::make('viewPublicProfile')
                                    ->url(fn () => route('users.profile.view.public', ['user' => $user->public_profile_slug]))
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->openUrlInNewTab()
                                    ->visible(fn () => $user->public_profile_slug),
                            )
                            ->live(),
                    ]),
                Section::make('Profile Information')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->label('Avatar')
                            ->visibility('private')
                            ->disk('s3')
                            ->collection('avatar')
                            ->hidden($user->is_external)
                            ->avatar(),
                        Placeholder::make('external_avatar')
                            ->label('Avatar')
                            ->content('Your authentication into this application is managed through single sign on (SSO). Please update your profile picture in your source authentication system and then logout and login here to persist that update into this application.')
                            ->visible($user->is_external),
                        $this->getNameFormComponent()
                            ->disabled($user->is_external),
                        $this->getEmailFormComponent()
                            ->disabled($user->is_external),
                        Checkbox::make('is_email_visible_on_profile')
                            ->label('Show Email on profile')
                            ->live()
                            ->visible($hasCrmLicense),
                        RichEditor::make('bio')
                            ->label('Personal Bio')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'blockquote', 'bulletList', 'orderedList'])
                            ->hint(fn (Get $get): string => $get('is_bio_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('is_bio_visible_on_profile')
                            ->label('Show Bio on profile')
                            ->visible($hasCrmLicense)
                            ->live(),
                        PhoneInput::make('phone_number')
                            ->label('Contact phone number')
                            ->hint(fn (Get $get): string => $get('is_phone_number_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('is_phone_number_visible_on_profile')
                            ->label('Show phone number on profile')
                            ->live()
                            ->visible($hasCrmLicense),
                        Select::make('pronouns_id')
                            ->relationship('pronouns', 'label')
                            ->hint(fn (Get $get): string => $get('are_pronouns_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('are_pronouns_visible_on_profile')
                            ->label('Show Pronouns on profile')
                            ->live()
                            ->visible($hasCrmLicense),
                        Placeholder::make('teams')
                            ->label('Team')
                            ->content(fn () => $user->team->name)
                            ->hidden(! $user->team)
                            ->hint(fn (Get $get): string => $get('are_teams_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        //TODO: Right now this is not passed to the frontend
                        Checkbox::make('are_teams_visible_on_profile')
                            ->label('Show ' . str('team') . ' on profile')
                            ->hidden(! $user->team)
                            ->live(),
                        Placeholder::make('division')
                            ->content($user->team?->division?->name)
                            ->hidden(! $user->team?->division()->exists())
                            ->hint(fn (Get $get): string => $get('is_division_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        //TODO: Right now this is not passed to the frontend
                        Checkbox::make('is_division_visible_on_profile')
                            ->label('Show Division on profile')
                            ->hidden(! $user->team?->division()->exists())
                            ->live(),
                        $this->getPasswordFormComponent()
                            ->hidden($user->is_external),
                        $this->getPasswordConfirmationFormComponent()
                            ->hidden($user->is_external),
                    ]),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::auth/pages/edit-profile.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::auth/pages/edit-profile.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->hint(fn (Get $get): string => $get('is_email_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::auth/pages/edit-profile.form.password.label'))
            ->password()
            ->rule(Password::default())
            ->autocomplete('new-password')
            ->dehydrated(fn ($state): bool => filled($state))
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::auth/pages/edit-profile.form.password_confirmation.label'))
            ->password()
            ->required()
            ->visible(fn (Get $get): bool => filled($get('password')))
            ->dehydrated(false);
    }
}
