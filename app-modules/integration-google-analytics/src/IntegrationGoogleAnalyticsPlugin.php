<?php

namespace Assist\IntegrationGoogleAnalytics;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentView;
use Assist\IntegrationGoogleAnalytics\Settings\GoogleAnalyticsSettings;

class IntegrationGoogleAnalyticsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'integration-google-analytics';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverPages(
            in: __DIR__ . '/Filament/Pages',
            for: 'Assist\\IntegrationGoogleAnalytics\\Filament\\Pages'
        );
    }

    public function boot(Panel $panel): void
    {
        $settings = app(GoogleAnalyticsSettings::class);

        if ($settings->is_enabled) {
            if (filled($settings->id)) {
                $script = "
                    <script async src='https://www.googletagmanager.com/gtag/js?id={$settings->id}'></script>
                    <script>
                        window.dataLayer = window.dataLayer || [];
                        function gtag(){window.dataLayer.push(arguments);}
                        gtag('js', new Date());

                        gtag('config', '{$settings->id}');
                    </script>
                ";

                FilamentView::registerRenderHook(
                    'panels::head.start',
                    fn (): string => Blade::render($script),
                );
            }
        }
    }
}
