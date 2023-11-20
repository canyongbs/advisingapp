<?php

use Illuminate\Support\Facades\Route;
use Assist\Webhook\Http\Middleware\HandleAwsSnsRequest;
use Assist\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use Assist\IntegrationAwsSesEventHandling\Http\Controllers\AwsSesInboundWebhookController;

Route::post('/inbound/webhook/awsses', AwsSesInboundWebhookController::class)
    ->middleware(
        [
            VerifyAwsSnsRequest::class,
            HandleAwsSnsRequest::class,
        ]
    )
    ->name('inbound.webhook.awsses');
