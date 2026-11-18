<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Pages;

use AdvisingApp\Engagement\Livewire\EmailTemplatesTable;
use AdvisingApp\Engagement\Livewire\SmsTemplatesTable;
use App\Enums\CommunicationSettingsTab;
use App\Enums\NavigationGroup;
use App\Models\User;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use UnitEnum;

/**
 * @property-read Schema $notificationSettingsForm
 */
class ManageCommunicationSettings extends Page
{
    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Settings;

    protected static ?string $navigationLabel = 'Communication Settings';

    protected static ?string $title = 'Communication Settings';

    protected static ?int $navigationSort = 40;

    #[Url(as: 'tab')]
    public ?string $activeTab = null;

    /** @var array<string, mixed>|null */
    public ?array $notificationSettingsData = [];

    public static function canAccess(): bool
    {
        return collect(CommunicationSettingsTab::cases())
            ->contains(fn (CommunicationSettingsTab $tab): bool => $tab->canView());
    }

    public function mount(): void
    {
        $this->fillNotificationSettingsForm();

        if (! CommunicationSettingsTab::tryFrom($this->activeTab ?? '')?->canView()) {
            $this->activeTab = collect(CommunicationSettingsTab::cases())
                ->first(fn (CommunicationSettingsTab $tab): bool => $tab->canView())?->value;
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        CommunicationSettingsTab::Notifications->value => $this->makeTab(CommunicationSettingsTab::Notifications)
                            ->schema([
                                $this->makeFormContentComponent('notificationSettingsForm', 'saveNotificationSettings'),
                            ]),
                        CommunicationSettingsTab::Email->value => $this->makeTab(CommunicationSettingsTab::Email)
                            ->schema([
                                Livewire::make(EmailTemplatesTable::class),
                            ]),
                        CommunicationSettingsTab::Text->value => $this->makeTab(CommunicationSettingsTab::Text)
                            ->schema([
                                Livewire::make(SmsTemplatesTable::class),
                            ]),
                    ]),
            ]);
    }

    public function notificationSettingsForm(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->disabled(! $this->canUpdateNotificationSettings())
            ->statePath('notificationSettingsData')
            ->components([
                TextInput::make('from_name')
                    ->string()
                    ->maxLength(150)
                    ->autocomplete(false),
                ColorSelect::make('primary_color')
                    ->shadeOptions(),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->disk('s3-public')
                    ->collection('logo')
                    ->visibility('public')
                    ->model(NotificationSettings::getSettingsPropertyModel('notifications.logo'))
                    ->image()
                    ->maxSize(10240),
            ]);
    }

    public function saveNotificationSettings(): void
    {
        abort_unless($this->canUpdateNotificationSettings(), 403);

        $data = $this->notificationSettingsForm->getState();

        $settings = app(NotificationSettings::class);
        $settings->fill($data);
        $settings->save();

        Notification::make()
            ->success()
            ->title(__('filament-spatie-laravel-settings-plugin::pages/settings-page.notifications.saved.title'))
            ->send();
    }

    protected function fillNotificationSettingsForm(): void
    {
        $settings = app(NotificationSettings::class);

        $this->notificationSettingsForm->fill($settings->toArray());
    }

    protected function makeTab(CommunicationSettingsTab $tab): Tab
    {
        return Tab::make($tab->getLabel())
            ->visible(fn (): bool => $tab->canView());
    }

    protected function makeFormContentComponent(string $schema, string $submitHandler): Component
    {
        return Form::make([EmbeddedSchema::make($schema)])
            ->id($schema)
            ->livewireSubmitHandler($submitHandler)
            ->footer([
                Actions::make([
                    Action::make($submitHandler)
                        ->label('Save')
                        ->submit($submitHandler)
                        ->visible(fn (): bool => $this->canUpdateNotificationSettings()),
                ]),
            ]);
    }

    protected function canUpdateNotificationSettings(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return $user->can('settings.*.update');
    }
}
