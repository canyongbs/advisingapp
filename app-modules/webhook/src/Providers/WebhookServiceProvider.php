<?php

namespace Assist\Webhook\Providers;

use Filament\Panel;
use Assist\Webhook\WebhookPlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Webhook\Models\InboundWebhook;
use Illuminate\Database\Eloquent\Relations\Relation;

class WebhookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new WebhookPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'inbound_webhook' => InboundWebhook::class,
        ]);
    }
}
