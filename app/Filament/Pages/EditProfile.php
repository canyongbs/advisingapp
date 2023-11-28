<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Pages;

use Exception;
use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Contracts\Auth\Authenticatable;
use Assist\MeetingCenter\Managers\CalendarManager;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;

/**
 * @property Form $form
 */
class EditProfile extends Page
{
    use InteractsWithFormActions;

    protected static string $view = 'filament.pages.edit-profile';

    protected static ?string $slug = 'profile';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    //TODO: I feel like a lot of these could be refactored into a settings file instead of adding them directly to the user migration.
    public function form(Form $form): Form
    {
        /** @var User $user */
        $user = auth()->user();

        $connectedAccounts = collect([
            Grid::make()
                ->schema([
                    Placeholder::make('calendar')
                        ->label(function (): string {
                            /** @var User $user */
                            $user = auth()->user();

                            return "{$user->calendar->provider_type->getLabel()} Calendar";
                        })
                        ->content(function (): ?string {
                            /** @var User $user */
                            $user = auth()->user();

                            return $user->calendar?->name;
                        }),
                    Actions::make([
                        FormAction::make('Disconnect')
                            ->icon('heroicon-m-trash')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function () {
                                /** @var User $user */
                                $user = auth()->user();

                                $calendar = $user->calendar;

                                $revoked = resolve(CalendarManager::class)
                                    ->driver($calendar->provider_type->value)
                                    ->revokeToken($calendar);

                                if ($revoked) {
                                    $calendar->delete();

                                    Notification::make()
                                        ->title("Disconnected {$calendar->provider_type->getLabel()} Calendar")
                                        ->success()
                                        ->send();
                                }
                            }),
                    ])->alignRight()
                        ->verticallyAlignCenter(),
                ])
                ->visible(function (): bool {
                    /** @var User $user */
                    $user = auth()->user();

                    return filled($user->calendar?->oauth_token);
                }),
        ])->filter(fn (Component $component) => $component->isVisible());

        $officeHoursDays = collect([
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ])->map(
            fn ($day) => Grid::make(3)
                ->schema([
                    Toggle::make("office_hours_days.{$day}.enabled")
                        ->label(str($day)->ucfirst())
                        ->inline(false)
                        ->live(),
                    TimePicker::make("office_hours_days.{$day}.start")
                        ->visible(fn (Get $get) => $get("office_hours_days.{$day}.enabled")),
                    TimePicker::make("office_hours_days.{$day}.end")
                        ->visible(fn (Get $get) => $get("office_hours_days.{$day}.enabled")),
                ])
        )->toArray();

        return $form
            ->schema([
                Section::make('Public')
                    ->aside()
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
                            ->prefix(str(route('view-public-user-profile', ['user' => $user]))->beforeLast('/')->append('/')),
                    ]),
                Section::make('Profile Information')
                    ->description('This information is visible to other users on your profile page, if you choose to make it visible.')
                    ->aside()
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
                            ->label('Show ' . str('team')->plural($user->teams->count())->ucfirst() . ' on profile')
                            ->hidden($user->teams->isEmpty())
                            ->live(),
                        Placeholder::make('division')
                            ->content($user->teams->first()?->division?->name)
                            ->hidden(! $user->teams?->first()?->division()->exists())
                            ->hint(fn (Get $get): string => $get('is_division_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('is_division_visible_on_profile')
                            ->label('Show Division on profile')
                            ->hidden(! $user->teams?->first()?->division()->exists())
                            ->live(),
                        Placeholder::make('external_avatar')
                            ->label('Avatar')
                            ->content('Your authentication into this application is managed through single sign on (SSO). Please update your profile picture in your source authentication system and then logout and login here to persist that update into this application.')
                            ->visible($user->is_external),
                    ]),
                Section::make('Account Information')
                    ->description('Update your account\'s information.')
                    ->aside()
                    ->schema([
                        $this->getEmailFormComponent()
                            ->disabled($user->is_external),
                        $this->getPasswordFormComponent()
                            ->hidden($user->is_external),
                        $this->getPasswordConfirmationFormComponent()
                            ->hidden($user->is_external),
                        TimezoneSelect::make('timezone')
                            ->required()
                            ->selectablePlaceholder(false),
                    ]),
                Section::make('Connected Accounts')
                    ->description('Disconnect your external accounts.')
                    ->aside()
                    ->schema($connectedAccounts->toArray())
                    ->visible($connectedAccounts->count()),
                Section::make('Office Hours')
                    ->aside()
                    ->schema([
                        Toggle::make('office_hours_are_enabled')
                            ->label('Enable office hours')
                            ->live(),
                        Toggle::make('appointments_are_restricted_to_existing_students')
                            ->label('Restrict appointments to existing students')
                            ->visible(fn (Get $get) => $get('office_hours_are_enabled')),
                        Section::make('Days')
                            ->schema($officeHoursDays)
                            ->visible(fn (Get $get) => $get('office_hours_are_enabled')),
                        Grid::make(3)
                            ->schema([
                                Toggle::make('out_of_office_is_enabled')
                                    ->label('Out of office')
                                    ->inline(false)
                                    ->live()
                                    ->visible(fn (Get $get) => $get('office_hours_are_enabled')),
                                DateTimePicker::make('out_of_office_starts_at')
                                    ->label('Start')
                                    ->visible(fn (Get $get) => $get('out_of_office_is_enabled')),
                                DateTimePicker::make('out_of_office_ends_at')
                                    ->label('End')
                                    ->visible(fn (Get $get) => $get('out_of_office_is_enabled')),
                            ]),
                    ]),
            ]);
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    public function getUser(): Authenticatable|Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    public function save(): void
    {
        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getUser(), $data);

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            return;
        }

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $data['password'],
            ]);
        }

        $this->data['password'] = null;
        $this->data['passwordConfirmation'] = null;

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }
    }

    public function getFormActionsAlignment(): string
    {
        return Alignment::Start->value;
    }

    protected function fillForm(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    protected function getSavedNotification(): ?Notification
    {
        $title = $this->getSavedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($this->getSavedNotificationTitle());
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('filament-panels::pages/auth/edit-profile.notifications.saved.title');
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::pages/auth/edit-profile.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
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
            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
            ->password()
            ->required()
            ->visible(fn (Get $get): bool => filled($get('password')))
            ->dehydrated(false);
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data'),
            ),
        ];
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::pages/auth/edit-profile.actions.cancel.label'))
            ->url(filament()->getUrl())
            ->color('gray');
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
