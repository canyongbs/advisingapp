<?php

use Illuminate\Support\Facades\Route;
use Assist\IntegrationTwilio\Http\Middleware\EnsureTwilioRequestIsValid;
use Assist\IntegrationTwilio\Http\Controllers\TwilioInboundWebhookController;

Route::post('/inbound/webhook/twilio/{event}', TwilioInboundWebhookController::class)
    ->middleware(EnsureTwilioRequestIsValid::class)
    ->name('inbound.webhook.twilio');
