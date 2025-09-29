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

namespace App\Providers\Filament;

use Filament\Panel;
use App\Models\Tenant;
use Filament\PanelProvider;
use Filament\Actions\Action;
use Filament\Support\Assets\Js;
use App\Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Enums\Width;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Support\Assets\Asset;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\Column;
use Filament\View\PanelsRenderHook;
use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Vite;
use App\Filament\Pages\ProductHealth;
use Illuminate\Support\Facades\Blade;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Infolists\Components\Entry;
use Filament\Navigation\NavigationGroup;
use App\Settings\CollegeBrandingSettings;
use App\Filament\Clusters\ProfileSettings;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Contracts\Support\Htmlable;
use AdvisingApp\Theme\Settings\ThemeSettings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Multitenancy\Http\Middleware\NeedsTenant;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use AdvisingApp\Authorization\Filament\Pages\Auth\Login;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Illuminate\Foundation\ViteManifestNotFoundException;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        Field::configureUsing(fn ($field) => $field->translateLabel());
        Entry::configureUsing(fn ($entry) => $entry->translateLabel());
        Column::configureUsing(fn ($column) => $column->translateLabel());
        ExportAction::configureUsing(fn (ExportAction $action) => $action->maxRows(100000));
        ImportAction::configureUsing(fn (ImportAction $action) => $action->maxRows(100000));
        TiptapEditor::configureUsing(fn (TiptapEditor $editor) => $editor->gridLayouts([
            'two-columns',
            'three-columns',
            'four-columns',
            'asymmetric-left-thirds',
            'asymmetric-right-thirds',
        ]));
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login(Login::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->favicon(function () {
                if (! Tenant::checkCurrent()) {
                    return asset('/images/default-favicon-251024.png');
                }

                $themeSettings = app(ThemeSettings::class);
                $favicon = $themeSettings::getSettingsPropertyModel('theme.is_favicon_active')->getFirstMedia('favicon');

                return $themeSettings->is_favicon_active && $favicon ? $favicon->getTemporaryUrl(now()->addMinutes(5)) : asset('/images/default-favicon-251024.png');
            })
            ->assets($this->getAssets())
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->maxContentWidth('full')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                NeedsTenant::class,
                StartSession::class,
                EnsureValidTenantSession::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Artificial Intelligence')
                    ->icon('heroicon-o-sparkles')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('CRM')
                    ->icon('heroicon-o-academic-cap')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Premium Features')
                    ->icon('heroicon-o-rocket-launch')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Knowledge Management')
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Project Management')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Online Admissions')
                    ->icon('heroicon-o-document-plus')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Digital Forms')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Event Management')
                    ->icon('heroicon-o-calendar-days')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Data and Analytics')
                    ->icon('heroicon-o-circle-stack')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('User Management')
                    ->icon('heroicon-o-users')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Global Administration')
                    ->icon('heroicon-o-adjustments-vertical')
                    ->collapsed(),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(ProductHealth::class),
                FilamentFullCalendarPlugin::make(),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profile Settings')
                    ->url(fn () => ProfileSettings::getUrl())
                    ->icon('heroicon-s-cog-6-tooth'),
                Action::make('about')
                    ->label('About')
                    ->modalHeading('Advising App® by Canyon GBS')
                    ->modalDescription('Version ' . config('sentry.release'))
                    ->modalContent(fn () => view('components.about-modal'))
                    ->modalFooterActions([])
                    ->modalWidth(Width::Small)
                    ->icon('heroicon-s-information-circle'),
                MenuItem::make()
                    ->label('Recent Updates')
                    ->url(function (ThemeSettings $themeSettings) {
                        return $themeSettings->recent_updates_url;
                    })
                    ->icon('heroicon-s-megaphone')
                    ->openUrlInNewTab()
                    ->visible(function (ThemeSettings $themeSettings) {
                        return $themeSettings->is_recent_updates_url_enabled && ! empty($themeSettings->recent_updates_url);
                    }),
                MenuItem::make()
                    ->label('Get Support')
                    ->url(function (ThemeSettings $themeSettings) {
                        return $themeSettings->support_url;
                    })
                    ->icon('heroicon-s-lifebuoy')
                    ->openUrlInNewTab()
                    ->visible(function (ThemeSettings $themeSettings) {
                        return $themeSettings->is_support_url_enabled && ! empty($themeSettings->support_url);
                    }),
            ])
            ->colors(fn (ThemeSettings $themeSettings): array => array_merge(config('default-colors'), $themeSettings->color_overrides))
            ->renderHook(
                'panels::scripts.before',
                fn () => view('filament.scripts.scroll-sidebar-to-active-menu-item'),
            )
            ->renderHook(
                'panels::head.end',
                fn (ThemeSettings $themeSettings) => ($themeSettings->url) ? view('filament.layout.theme', ['url' => $themeSettings->url]) : null,
            )
            ->bootUsing(function (Panel $panel) {
                if (! Tenant::current()) {
                    return;
                }

                $panel->darkMode(app(ThemeSettings::class)->has_dark_mode);
            })
            ->renderHook(
                PanelsRenderHook::TOPBAR_AFTER,
                function (): ?Htmlable  {
                    $collegeBrandingSettings = app(CollegeBrandingSettings::class);

                    if (! $collegeBrandingSettings->is_enabled) {
                        return null;
                    }

                    return new HtmlString(Blade::render('<livewire:branding-bar />'));
                },
            );
    }

    public function boot(): void {}

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        try {
            return [
                Js::make('admin', url(Vite::asset('resources/js/admin.js'))),
            ];
        } catch (ViteManifestNotFoundException) {
            // If Vite has not been built yet, do not throw an exception.
            // Vite is not built when linting the application in CI, since
            // Larastan is not actually static and boots the application.
            return [];
        }
    }
}
