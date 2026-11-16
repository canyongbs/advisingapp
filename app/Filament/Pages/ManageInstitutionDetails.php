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

use App\Enums\InstitutionDetailsTab;
use App\Enums\NavigationGroup;
use App\Filament\Pages\Schemas\CollegeBrandingForm;
use App\Filament\Pages\Schemas\DisplaySettingsForm;
use App\Filament\Pages\Schemas\InstitutionDetailsProfileForm;
use App\Models\User;
use App\Settings\CollegeBrandingSettings;
use App\Settings\DisplaySettings;
use App\Settings\InstitutionDetailsSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use UnitEnum;

/**
 * @property-read Schema $profileForm
 * @property-read Schema $brandingForm
 * @property-read Schema $displayForm
 */
class ManageInstitutionDetails extends Page
{
    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Settings;

    protected static ?string $navigationLabel = 'Institution Details';

    protected static ?string $title = 'Institution Details';

    protected static ?int $navigationSort = 70;

    #[Url(as: 'tab')]
    public ?string $activeTab = null;

    /** @var array<string, mixed>|null */
    public ?array $profileData = [];

    /** @var array<string, mixed>|null */
    public ?array $brandingData = [];

    /** @var array<string, mixed>|null */
    public ?array $displayData = [];

    public static function canAccess(): bool
    {
        return collect(InstitutionDetailsTab::cases())
            ->contains(fn (InstitutionDetailsTab $tab): bool => $tab->canView());
    }

    public function mount(): void
    {
        $this->profileForm->fill(app(InstitutionDetailsSettings::class)->toArray());
        $this->brandingForm->fill(app(CollegeBrandingSettings::class)->toArray());
        $this->displayForm->fill(app(DisplaySettings::class)->toArray());

        if (! InstitutionDetailsTab::tryFrom($this->activeTab ?? '')?->canView()) {
            $this->activeTab = collect(InstitutionDetailsTab::cases())
                ->first(fn (InstitutionDetailsTab $tab): bool => $tab->canView())?->value;
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->key('tabs')
                    ->livewireProperty('activeTab')
                    ->tabs([
                        InstitutionDetailsTab::Profile->value => $this->makeTab(InstitutionDetailsTab::Profile)
                            ->schema([
                                $this->makeFormContentComponent('profileForm', 'saveProfile'),
                            ]),
                        InstitutionDetailsTab::Branding->value => $this->makeTab(InstitutionDetailsTab::Branding)
                            ->schema([
                                $this->makeFormContentComponent('brandingForm', 'saveBranding'),
                            ]),
                        InstitutionDetailsTab::DatesAndTimes->value => $this->makeTab(InstitutionDetailsTab::DatesAndTimes)
                            ->schema([
                                $this->makeFormContentComponent('displayForm', 'saveDisplay'),
                            ]),
                    ]),
            ]);
    }

    public function profileForm(Schema $schema): Schema
    {
        return InstitutionDetailsProfileForm::configure($schema)
            ->statePath('profileData')
            ->disabled(! $this->canUpdateSettings());
    }

    public function brandingForm(Schema $schema): Schema
    {
        return CollegeBrandingForm::configure($schema)
            ->statePath('brandingData')
            ->disabled(! $this->canUpdateSettings());
    }

    public function displayForm(Schema $schema): Schema
    {
        return DisplaySettingsForm::configure($schema)
            ->statePath('displayData')
            ->disabled(! $this->canUpdateSettings());
    }

    public function saveProfile(): void
    {
        abort_unless($this->canUpdateSettings(), 403);

        $settings = app(InstitutionDetailsSettings::class);
        $settings->fill($this->profileForm->getState());
        $settings->save();

        $this->getSavedNotification()->send();
    }

    public function saveBranding(): void
    {
        abort_unless($this->canUpdateSettings(), 403);

        $settings = app(CollegeBrandingSettings::class);
        $settings->fill($this->brandingForm->getState());
        $settings->save();

        if (! $settings->dismissible) {
            User::query()->update(['is_branding_bar_dismissed' => false]);
        }

        $this->getSavedNotification()->send();
    }

    public function saveDisplay(): void
    {
        abort_unless($this->canUpdateSettings(), 403);

        $settings = app(DisplaySettings::class);
        $settings->fill($this->displayForm->getState());
        $settings->save();

        $this->getSavedNotification()->send();
    }

    protected function makeTab(InstitutionDetailsTab $tab): Tab
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
                        ->label(__('filament-spatie-laravel-settings-plugin::pages/settings-page.form.actions.save.label'))
                        ->submit($submitHandler)
                        ->keyBindings(['mod+s'])
                        ->visible(fn (): bool => $this->canUpdateSettings()),
                ])
                    ->key("{$schema}-actions"),
            ]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('filament-spatie-laravel-settings-plugin::pages/settings-page.notifications.saved.title'));
    }

    protected function canUpdateSettings(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->can('settings.*.update');
    }
}
