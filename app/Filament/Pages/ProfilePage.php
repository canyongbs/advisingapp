<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use App\Filament\Clusters\ProfileSettings;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Schema $form
 */
abstract class ProfilePage extends Page
{
    use InteractsWithFormActions;

    protected string $view = 'filament.pages.profile-save';

    protected static ?string $cluster = ProfileSettings::class;

    /** @var array<string, mixed> $data */
    public ?array $data = [];

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

    public function fillForm(): void
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
    public function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    public function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    public function getSavedNotification(): ?Notification
    {
        $title = $this->getSavedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($this->getSavedNotificationTitle());
    }

    public function getSavedNotificationTitle(): ?string
    {
        return __('filament-panels::auth/pages/edit-profile.notifications.saved.title');
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    public function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::auth/pages/edit-profile.actions.cancel.label'))
            ->url(filament()->getUrl())
            ->color('gray');
    }

    public function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label('Save')
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->operation('edit')
            ->model($this->getUser())
            ->statePath('data');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    /**
     * @return array<string, Grid>
     */
    protected function getHoursForDays(string $key): array
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
            fn ($day) => Flex::make([
                Toggle::make("{$key}.{$day}.enabled")
                    ->label(str($day)->ucfirst())
                    ->inline(false)
                    ->live(),
                Flex::make([
                    TimePicker::make("{$key}.{$day}.starts_at")
                        ->required()
                        ->visible(fn (Get $get) => $get("{$key}.{$day}.enabled")),
                    TimePicker::make("{$key}.{$day}.ends_at")
                        ->required()
                        ->visible(fn (Get $get) => $get("{$key}.{$day}.enabled")),
                ]),

                Actions::make([
                    Action::make("copy_time_from_{$day}_{$key}")
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
