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

namespace Assist\IntegrationMicrosoftClarity;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentView;
use Assist\IntegrationMicrosoftClarity\Settings\MicrosoftClaritySettings;

class IntegrationMicrosoftClarityPlugin implements Plugin
{
    public function getId(): string
    {
        return 'integration-microsoft-clarity';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverPages(
            in: __DIR__ . '/Filament/Pages',
            for: 'Assist\\IntegrationMicrosoftClarity\\Filament\\Pages'
        );
    }

    public function boot(Panel $panel): void
    {
        $settings = app(MicrosoftClaritySettings::class);

        if ($settings->is_enabled) {
            if (filled($settings->id)) {
                $script = "
                    <script type='text/javascript'>
                        (function(c,l,a,r,i,t,y){
                            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                            t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;
                            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
                        })(window, document, 'clarity', 'script', '{$settings->id}');
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
