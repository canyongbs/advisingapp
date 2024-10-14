<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\ConstituentManagement;
use App\Settings\ManageStudentConfigurationSettings;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SettingsPage;
use Filament\Support\Facades\FilamentView;
use Throwable;

use function Filament\Support\is_app_url;

class ManageStudentConfiguration extends SettingsPage
{
    protected static string $settings = ManageStudentConfigurationSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $view = 'filament.pages.manage-student-configuration';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    protected static ?string $navigationLabel  = 'Configuration';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('is_enabled')
                    ->label('Enable')
                    ->default(false),
            ]);
    }

    public function save(): void
    {
        try {
            $this->callHook('beforeValidate');
            $data = $this->form->getState();

            $this->callHook('afterValidate');
            $settings = app(static::getSettings());
            $this->callHook('beforeSave');
            $settings->fill($data);
            $settings->save();

            $this->callHook('afterSave');

            Notification::make()
                ->title('Students is enabled!')
                ->success()
                ->send();

            if ($redirectUrl = $this->getRedirectUrl()) {
                $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
            }
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Something went wrong, if this continues please contact support.')
                ->danger()
                ->send();
        }
    }

    public function getRedirectUrl(): ?string
    {
        return ManageStudentConfiguration::getUrl();
    }
}
