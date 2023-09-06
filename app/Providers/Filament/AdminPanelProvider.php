<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Forms\Components\Field;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\UserResource;
use Filament\Infolists\Components\Entry;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Session\Middleware\StartSession;
use Assist\Task\Filament\Resources\TaskResource;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Assist\Audit\Filament\Resources\AuditResource;
use Assist\Authorization\Filament\Pages\Auth\Login;
use Assist\Audit\Filament\Pages\ManageAuditSettings;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Assist\Authorization\Filament\Resources\RoleResource;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Assist\Engagement\Filament\Resources\EngagementResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Assist\Webhook\Filament\Resources\InboundWebhookResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Authorization\Filament\Resources\RoleGroupResource;
use Assist\Prospect\Filament\Resources\ProspectSourceResource;
use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Assist\Authorization\Filament\Resources\PermissionResource;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;

class AdminPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        Field::configureUsing(fn ($field) => $field->translateLabel());
        Entry::configureUsing(fn ($entry) => $entry->translateLabel());
        Column::configureUsing(fn ($column) => $column->translateLabel());
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
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        NavigationItem::make('Dashboard')
                            ->icon('heroicon-o-home')
                            ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                            ->url(fn (): string => Dashboard::getUrl()),
                    ])
                    ->groups([
                        NavigationGroup::make(__('Records'))
                            ->items([
                                ...StudentResource::getNavigationItems(),
                                ...ProspectResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make(__('Tools'))
                            ->items([
                                ...EngagementResource::getNavigationItems(),
                                ...ServiceRequestResource::getNavigationItems(),
                                ...TaskResource::getNavigationItems(),
                                ...KnowledgeBaseItemResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make(__('Administration'))
                            ->items([
                                ...UserResource::getNavigationItems(),
                                ...RoleGroupResource::getNavigationItems(),
                                ...RoleResource::getNavigationItems(),
                                ...PermissionResource::getNavigationItems(),
                                ...AuditResource::getNavigationItems(),
                                ...InboundWebhookResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make(__('Settings'))
                            ->items([
                                ...ManageAuditSettings::getNavigationItems(),
                                ...ProspectStatusResource::getNavigationItems(),
                                ...ProspectSourceResource::getNavigationItems(),
                                ...ServiceRequestPriorityResource::getNavigationItems(),
                                ...ServiceRequestStatusResource::getNavigationItems(),
                                ...ServiceRequestTypeResource::getNavigationItems(),
                                ...KnowledgeBaseCategoryResource::getNavigationItems(),
                                ...KnowledgeBaseQualityResource::getNavigationItems(),
                                ...KnowledgeBaseStatusResource::getNavigationItems(),
                            ]),
                    ]);
            });
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
