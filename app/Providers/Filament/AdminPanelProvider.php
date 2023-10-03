<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Forms\Components\Field;
use App\Filament\Pages\ProductHealth;
use App\Filament\Actions\ImportAction;
use Filament\Infolists\Components\Entry;
use Filament\Navigation\NavigationGroup;
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
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            // ->maxContentWidth('full') //TODO: evaluate use
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
                    ->label('Record Management'),
                NavigationGroup::make()
                    ->label('Productivity Tools'),
                NavigationGroup::make()
                    ->label('Mass Engagement'),
                NavigationGroup::make()
                    ->label('Users and Permissions'),
                NavigationGroup::make()
                    ->label('Product Administration'),
                NavigationGroup::make()
                    ->label('Usage Analytics Integrations'),
                NavigationGroup::make()
                    ->label('Product Settings')
                    ->collapsed(),
            ])
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(ProductHealth::class),
            ]);
    }

    public function boot(): void
    {
        //If you change colors here also change in tailwind.config
        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::hex('#2bb8b3'),
            'success' => Color::Green,
            'warning' => Color::Amber,
            'trout' => [
                50 => '#f6f7f9',
                100 => '#ededf1',
                200 => '#d7d9e0',
                300 => '#b4b9c5',
                400 => '#8b92a5',
                500 => '#6d758a',
                600 => '#575d72',
                700 => '#4d5264',
                800 => '#3e424e',
                900 => '#363944',
                950 => '#24252d',
            ],
            'black' => [
                50 => '#f6f6f6',
                100 => '#e7e7e7',
                200 => '#d1d1d1',
                300 => '#b0b0b0',
                400 => '#888888',
                500 => '#6d6d6d',
                600 => '#5d5d5d',
                700 => '#4f4f4f',
                800 => '#454545',
                900 => '#3d3d3d',
                950 => '#000000',
            ],
            'white' => [
                50 => '#ffffff',
                100 => '#efefef',
                200 => '#dcdcdc',
                300 => '#bdbdbd',
                400 => '#989898',
                500 => '#7c7c7c',
                600 => '#656565',
                700 => '#525252',
                800 => '#464646',
                900 => '#3d3d3d',
                950 => '#292929',
            ],
            'dodger-blue' => [
                50 => '#eef3ff',
                100 => '#dae4ff',
                200 => '#bcd0ff',
                300 => '#8fb3ff',
                400 => '#5989ff',
                500 => '#3f69fe',
                600 => '#1d3ef3',
                700 => '#152ae0',
                800 => '#1824b5',
                900 => '#19258f',
                950 => '#141957',
            ],
            'java' => [
                50 => '#f1fcfa',
                100 => '#d1f6f1',
                200 => '#a4ebe3',
                300 => '#6edad1',
                400 => '#40c1bb',
                500 => '#2bb8b3',
                600 => '#1c8583',
                700 => '#1b6a6a',
                800 => '#1a5555',
                900 => '#1a4747',
                950 => '#09292a',
            ],
            'bright-sun' => [
                50 => '#fffbeb',
                100 => '#fff4c6',
                200 => '#fee989',
                300 => '#fed43f',
                400 => '#fec321',
                500 => '#f8a208',
                600 => '#db7a04',
                700 => '#b65607',
                800 => '#94420c',
                900 => '#79370e',
                950 => '#461b02',
            ],
            'jungle-mist' => [
                50 => '#f3f8f8',
                100 => '#e0eded',
                200 => '#bed7d8',
                300 => '#9cc2c4',
                400 => '#6da0a3',
                500 => '#518589',
                600 => '#466e74',
                700 => '#3e5b60',
                800 => '#384e52',
                900 => '#324247',
                950 => '#1e2a2e',
            ],
            'deep-blush' => [
                50 => '#fcf3f9',
                100 => '#fbe8f6',
                200 => '#f8d2ee',
                300 => '#f4addf',
                400 => '#ec7ac8',
                500 => '#e151af',
                600 => '#cf3391',
                700 => '#b32376',
                800 => '#942061',
                900 => '#7c1f53',
                950 => '#4b0c2f',
            ],
        ]);
    }
}
