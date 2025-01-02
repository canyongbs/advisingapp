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

namespace AdvisingApp\IntegrationTwilio\DataTransferObjects;

use Illuminate\Http\Request;

class TwilioMessageReceivedData extends TwilioWebhookData
{
    public function __construct(
        public ?string $toCountry,
        public ?string $toState,
        public ?string $smsMessageSid,
        public ?string $numMedia,
        public ?string $toCity,
        public ?string $fromZip,
        public ?string $smsSid,
        public ?string $fromState,
        public ?string $smsStatus,
        public ?string $fromCity,
        public ?string $body,
        public ?string $fromCountry,
        public ?string $to,
        public ?string $messagingServiceSid,
        public ?string $toZip,
        public ?string $numSegments,
        public ?string $messageSid,
        public ?string $accountSid,
        public ?string $from,
        public ?string $apiVersion,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $data = $request->all();

        return new self(
            toCountry: $data['ToCountry'] ?? null,
            toState: $data['ToState'] ?? null,
            smsMessageSid: $data['SmsMessageSid'] ?? null,
            numMedia: $data['NumMedia'] ?? null,
            toCity: $data['ToCity'] ?? null,
            fromZip: $data['FromZip'] ?? null,
            smsSid: $data['SmsSid'] ?? null,
            fromState: $data['FromState'] ?? null,
            smsStatus: $data['SmsStatus'] ?? null,
            fromCity: $data['FromCity'] ?? null,
            body: $data['Body'] ?? null,
            fromCountry: $data['FromCountry'] ?? null,
            to: $data['To'] ?? null,
            messagingServiceSid: $data['MessagingServiceSid'] ?? null,
            toZip: $data['ToZip'] ?? null,
            numSegments: $data['NumSegments'] ?? null,
            messageSid: $data['MessageSid'] ?? null,
            accountSid: $data['AccountSid'] ?? null,
            from: $data['From'] ?? null,
            apiVersion: $data['ApiVersion'] ?? null,
        );
    }
}
