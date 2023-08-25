<?php

use Illuminate\Support\Facades\Route;
use Assist\Webhook\Http\Middleware\EnsureTwilioRequestIsValid;
use Assist\Webhook\Http\Controllers\TwilioInboundWebhookController;

// Potential event types are:
// status-update
// message-received
Route::post('/inbound/webhook/twilio/{event}', TwilioInboundWebhookController::class)
    ->middleware(EnsureTwilioRequestIsValid::class)
    ->name('inbound.webhook.twilio');
