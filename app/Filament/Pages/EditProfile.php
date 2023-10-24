<?php

namespace App\Filament\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;

class EditProfile extends BaseEditProfile
{
    public static function getLabel(): string
    {
        return 'User Profile';
    }

    public function form(Form $form): Form
    {
        $user = auth()->user();

        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('avatar')
                    ->label('Avatar')
                    ->visibility('private')
                    ->disk('s3')
                    ->collection('avatar')
                    ->hidden($user->is_external)
                    ->avatar(),
                $this->getNameFormComponent()
                    ->disabled($user->is_external),
                $this->getEmailFormComponent()
                    ->disabled($user->is_external),
                $this->getPasswordFormComponent()
                    ->hidden($user->is_external),
                $this->getPasswordConfirmationFormComponent()
                    ->hidden($user->is_external),
                TimezoneSelect::make('timezone')
                    ->required()
                    ->selectablePlaceholder(false),
                RichEditor::make('bio')
                    ->label('Personal Bio')
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'blockquote', 'bulletList', 'orderedList'])
                    ->hint(fn (Get $get): string => $get('is_bio_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                Checkbox::make('is_bio_visible_on_profile')
                    ->label('Show Bio on profile')
                    ->live(),
                Select::make('pronouns_id')
                    ->relationship('pronouns', 'label')
                    ->hint(fn (Get $get): string => $get('are_pronouns_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                Checkbox::make('are_pronouns_visible_on_profile')
                    ->label('Show Pronouns on profile')
                    ->live(),
                Placeholder::make('teams')
                    ->label(str('Team')->plural($user->teams->count()))
                    ->content($user->teams->pluck('name')->join(', ', ' and '))
                    ->hidden($user->teams->isEmpty())
                    ->hint(fn (Get $get): string => $get('are_teams_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                Checkbox::make('are_teams_visible_on_profile')
                    ->label('Show ' . str('team')->plural($user->teams->count()) . ' on profile')
                    ->live(),
                Placeholder::make('external_avatar')
                    ->label('Avatar')
                    ->content('Your authentication into this application is managed through single sign on (SSO). Please update your profile picture in your source authentication system and then logout and login here to persist that update into this application.')
                    ->visible($user->is_external),
            ]);
    }
}
