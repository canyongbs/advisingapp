<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\IntegrationTwilio\Http\Middleware;

use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telnyx\Telnyx;
use Telnyx\Webhook;
use Telnyx\WebhookSignature;
use Throwable;

class VerifyTelnyxWebhookSignature
{
    public function handle(Request $request, Closure $next): mixed
    {
        $signature = $request->header('telnyx-signature-ed25519');
        $timestamp = $request->header('telnyx-timestamp');

        if (blank($signature) || blank($timestamp)) {
            abort(403);
        }

        $settings = app(TwilioSettings::class);

        Telnyx::setApiKey($settings->telnyx_api_key);

        try {
            $verified = WebhookSignature::verifyHeader(
                payload: $request->getContent(),
                signature_header: $signature,
                timestamp: $timestamp,
                public_key: config('services.telnyx.public_key'),
                tolerance: Webhook::DEFAULT_TOLERANCE,
            );

            if (! $verified) {
                Log::info('Telnyx request could not be verified.', [
                    'signature' => $signature,
                    'timestamp' => $timestamp,
                    'data' => $request->getContent(),
                ]);

                abort(403);
            }
        } catch (Throwable $exception) {
            Log::info('Telnyx request could not be verified.', [
                'signature' => $signature,
                'timestamp' => $timestamp,
                'data' => $request->getContent(),
                'exception' => $exception->getMessage(),
            ]);

            abort(403);
        }

        return $next($request);
    }
}
