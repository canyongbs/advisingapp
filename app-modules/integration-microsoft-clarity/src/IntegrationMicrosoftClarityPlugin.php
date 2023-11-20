<?php

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
