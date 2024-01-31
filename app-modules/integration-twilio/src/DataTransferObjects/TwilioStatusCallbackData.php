<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

class TwilioStatusCallbackData extends TwilioWebhookData
{
    public function __construct(
        public ?string $messageSid,
        public ?string $accountSid,
        public ?string $apiVersion,
        public ?string $body,
        public ?string $dateCreated,
        public ?string $dateSent,
        public ?string $dateUpdated,
        public ?string $direction,
        public ?string $errorCode,
        public ?string $errorMessage,
        public ?string $from,
        public ?string $messagingServiceSid,
        public ?string $numMedia,
        public ?string $numSegments,
        public ?string $price,
        public ?string $priceUnit,
        public ?string $sid,
        public ?string $status,
        public ?string $to,
        public ?string $uri,
        public ?string $messageStatus,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $data = $request->all();

        return new static(
            messageSid: $data['MessageSid'] ?? null,
            accountSid: $data['account_sid'] ?? null,
            apiVersion: $data['api_version'] ?? null,
            body: $data['body'] ?? null,
            dateCreated: $data['date_created'] ?? null,
            dateSent: $data['date_sent'] ?? null,
            dateUpdated: $data['date_updated'] ?? null,
            direction: $data['direction'] ?? null,
            errorCode: $data['error_code'] ?? null,
            errorMessage: $data['error_message'] ?? null,
            from: $data['from'] ?? null,
            messagingServiceSid: $data['messaging_service_sid'] ?? null,
            numMedia: $data['num_media'] ?? null,
            numSegments: $data['num_segments'] ?? null,
            price: $data['price'] ?? null,
            priceUnit: $data['price_unit'] ?? null,
            sid: $data['sid'] ?? null,
            status: $data['status'] ?? null,
            to: $data['to'] ?? null,
            uri: $data['uri'] ?? null,
            messageStatus: $data['MessageStatus'] ?? null,
        );
    }
}
