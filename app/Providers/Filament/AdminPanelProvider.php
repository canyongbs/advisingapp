<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Forms\Components\Field;
use App\Filament\Actions\ImportAction;
use Filament\Infolists\Components\Entry;
use Filament\Navigation\NavigationGroup;
use App\Filament\Pages\ApplicationHealth;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Assist\Authorization\Filament\Pages\Auth\Login;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        Field::configureUsing(fn ($field) => $field->translateLabel());
        Entry::configureUsing(fn ($entry) => $entry->translateLabel());
        Column::configureUsing(fn ($column) => $column->translateLabel());
        ImportAction::configureUsing(fn (ImportAction $action) => $action->max(100000));
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login(Login::class)
            ->colors([
                'primary' => Color::hex('#2bb8b3'),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
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
                    ->label('Records'),
                NavigationGroup::make()
                    ->label('Tools'),
                NavigationGroup::make()
                    ->label('Administration'),
                NavigationGroup::make()
                    ->label('Integrations'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->collapsed(),
            ])
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(ApplicationHealth::class),
            ]);
    }

    public function boot(): void
    {
        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::hex('#2bb8b3'),
            'success' => Color::Green,
            'warning' => Color::Amber,
            'white' => Color::hex('#fff'),
        ]);
    }
}
