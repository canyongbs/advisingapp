<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Theme\Filament\Pages;

use Throwable;
use App\Models\User;
use App\Models\Tenant;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Clusters\GlobalSettings;
use AdvisingApp\Theme\Settings\ThemeSettings;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ManageBrandConfigurationSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Partner Branding';

    protected static ?int $navigationSort = 30;

    protected static string $settings = ThemeSettings::class;

    protected static ?string $title = 'Partner Branding';

    protected static ?string $cluster = GlobalSettings::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('theme.view_theme_settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Application Name')
                    ->aside()
                    ->schema([
                        TextInput::make('application_name')
                            ->label('Application Name')
                            ->required()
                            ->maxLength('255'),
                    ]),
                Section::make('Partner Favicon')
                    ->aside()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->disk('s3')
                            ->collection('favicon')
                            ->visibility('private')
                            ->image()
                            ->model(
                                ThemeSettings::getSettingsPropertyModel('theme.is_favicon_active'),
                            )
                            ->afterStateUpdated(fn (Set $set) => $set('is_favicon_active', true))
                            ->deleteUploadedFileUsing(fn (Set $set) => $set('is_favicon_active', false))
                            ->hiddenLabel(),
                        Toggle::make('is_favicon_active')
                            ->label('Active')
                            ->hidden(fn (Get $get): bool => blank($get('favicon'))),
                    ]),
                Section::make('Partner Logo')
                    ->aside()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->disk('s3')
                            ->collection('logo')
                            ->visibility('private')
                            ->image()
                            ->model(
                                ThemeSettings::getSettingsPropertyModel('theme.is_logo_active'),
                            )
                            ->afterStateUpdated(fn (Set $set) => $set('is_logo_active', true))
                            ->deleteUploadedFileUsing(fn (Set $set) => $set('is_logo_active', false))
                            ->hiddenLabel(),
                        SpatieMediaLibraryFileUpload::make('dark_logo')
                            ->disk('s3')
                            ->collection('dark_logo')
                            ->visibility('private')
                            ->image()
                            ->model(
                                ThemeSettings::getSettingsPropertyModel('theme.is_logo_active'),
                            )
                            ->hidden(fn (Get $get): bool => blank($get('logo'))),
                        Toggle::make('is_logo_active')
                            ->label('Active')
                            ->hidden(fn (Get $get): bool => blank($get('logo'))),
                    ]),
                Section::make('Branded Website Links')
                    ->aside()
                    ->schema([
                        Section::make('Support')
                            ->schema([
                                Toggle::make('is_support_url_enabled')
                                    ->label('Enable Support URL')
                                    ->live()
                                    ->columnSpanFull(),
                                TextInput::make('support_url')
                                    ->label('Support URL')
                                    ->url()
                                    ->visible(fn (Get $get) => $get('is_support_url_enabled'))
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Recent Updates')
                            ->schema([
                                Toggle::make('is_recent_updates_url_enabled')
                                    ->label('Enable Recent Updates URL')
                                    ->live()
                                    ->columnSpanFull(),
                                TextInput::make('recent_updates_url')
                                    ->label('Recent Updates URL')
                                    ->url()
                                    ->visible(fn (Get $get) => $get('is_recent_updates_url_enabled'))
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Custom Link')
                            ->schema([
                                Toggle::make('is_custom_link_url_enabled')
                                    ->label('Enable Custom Link URL')
                                    ->live()
                                    ->columnSpanFull(),
                                TextInput::make('custom_link_label')
                                    ->label('Custom Link URL Label')
                                    ->alphaNum()
                                    ->maxLength(16)
                                    ->visible(fn (Get $get) => $get('is_custom_link_url_enabled'))
                                    ->columnSpanFull(),
                                TextInput::make('custom_link_url')
                                    ->label('Custom Link URL')
                                    ->url()
                                    ->visible(fn (Get $get) => $get('is_custom_link_url_enabled'))
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Changelog URL')
                    ->aside()
                    ->schema([
                        TextInput::make('changelog_url')
                            ->label('Changelog URL')
                            ->url()
                            ->maxLength('255'),
                    ]),

                Section::make('Product Knowledge Base URL')
                    ->aside()
                    ->schema([
                        TextInput::make('product_knowledge_base_url')
                            ->label('Product Knowledge Base URL')
                            ->url()
                            ->maxLength('255'),
                    ]),
            ]);
    }

    public function save(): void
    {
        DB::beginTransaction();

        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            /** @var Tenant $tenant */
            $tenant = Tenant::current();

            /** @var TenantConfig $config */
            $config = $tenant->config;

            $config->applicationName = $data['application_name'] ?? config('app.name');

            $tenant->config = $config;

            $tenant->save();

            unset(
                $data['application_name'],
            );

            $settings = app(static::getSettings());

            $settings->fill($data);
            $settings->save();

            $this->callHook('afterSave');

            DB::commit();

            $this->getSavedNotification()?->send();

            if ($redirectUrl = $this->getRedirectUrl()) {
                $this->redirect($redirectUrl);
            }
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            Notification::make()
                ->title('Something went wrong, if this continues please contact support.')
                ->danger()
                ->send();
        }
    }

    public function getRedirectUrl(): ?string
    {
        // After saving, redirect to the current page to refresh
        // the logo preview in the layout.
        return ManageBrandConfigurationSettings::getUrl();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $settings = app(static::getSettings());

        /** @var Tenant $tenant */
        $tenant = Tenant::current();

        /** @var TenantConfig $config */
        $config = $tenant->config;

        $data = $this->mutateFormDataBeforeFill(
            [
                ...$settings->toArray(),
                'application_name' => $config->applicationName ?? config('app.name'),
            ]
        );

        $this->form->fill($data);

        $this->callHook('afterFill');
    }
}
