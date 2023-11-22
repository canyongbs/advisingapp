<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
