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
use App\Filament\Clusters\ProfileSettings;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @property Form $form
 */
class WorkingHours extends Page
{
    use InteractsWithFormActions;

    protected static string $view = 'filament.pages.profile-save';

    protected static ?string $cluster = ProfileSettings::class;

    protected static ?string $slug = 'working-hours';

    protected static ?string $title = 'Working Hours';

    protected static ?int $navigationSort = 70;

    protected static bool $shouldRegisterNavigation = true;

    /** @var array<string, mixed> $data */
    public ?array $data = [];

    public function form(Form $form): Form
    {
        /** @var User $user */
        $user = auth()->user();
        $hasCrmLicense = $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]);

        return $form
            ->schema([
                Section::make('Working Hours')
                    ->visible($hasCrmLicense)
                    ->schema([
                        Toggle::make('working_hours_are_enabled')
                            ->label('Set Working Hours')
                            ->live()
                            ->hint(fn (Get $get): string => $get('are_working_hours_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('are_working_hours_visible_on_profile')
                            ->label('Show Working Hours on profile')
                            ->visible(fn (Get $get) => $get('working_hours_are_enabled'))
                            ->live(),
                        Section::make('Days')
                            ->schema($this->getHoursForDays('working_hours'))
                            ->visible(fn (Get $get) => $get('working_hours_are_enabled')),
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

        $this->dispatch('refresh-branding-bar');

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
            ->unique(ignoreRecord: true)
            ->hint(fn (Get $get): string => $get('is_email_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile');
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
            ->label('Save')
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    /**
     * @return array<string, Grid>
     */
    private function getHoursForDays(string $key): array
    {
        return collect([
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ])->map(
            fn ($day) => Split::make([
                Toggle::make("{$key}.{$day}.enabled")
                    ->label(str($day)->ucfirst())
                    ->inline(false)
                    ->live(),
                Split::make([
                    TimePicker::make("{$key}.{$day}.starts_at")
                        ->required()
                        ->visible(fn (Get $get) => $get("{$key}.{$day}.enabled")),
                    TimePicker::make("{$key}.{$day}.ends_at")
                        ->required()
                        ->visible(fn (Get $get) => $get("{$key}.{$day}.enabled")),
                ]),

                Actions::make([
                    FormAction::make("copy_time_from_{$day}_{$key}")
                        ->label('Copy to All')
                        ->visible(fn (Get $get) => $get("{$key}.{$day}.enabled"))
                        ->link()
                        ->color('blue')
                        ->extraAttributes(['class' => 'fi-action-copytime-link'])
                        ->action(function (Get $get, Set $set) use ($day, $key) {
                            $start = $get("{$key}.{$day}.starts_at");
                            $end = $get("{$key}.{$day}.ends_at");

                            collect(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])
                                ->filter(fn ($targetDay) => $targetDay !== $day)
                                ->each(function ($targetDay) use ($get, $set, $key, $start, $end) {
                                    if ($get("{$key}.{$targetDay}.enabled") === false) {
                                        return;
                                    }
                                    $set("{$key}.{$targetDay}.starts_at", $start);
                                    $set("{$key}.{$targetDay}.ends_at", $end);
                                });

                            Notification::make()
                                ->title('Copied time to all days')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
                ->from('md')
                ->verticalAlignment(VerticalAlignment::End)
        )->toArray();
    }
}
