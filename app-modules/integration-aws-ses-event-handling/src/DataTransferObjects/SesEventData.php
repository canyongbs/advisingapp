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

namespace AdvisingApp\IntegrationAwsSesEventHandling\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SesEventData extends Data
{
    public function __construct(
        public string $eventType,
        public SesMailData $mail,
        public SesBounceData|Optional $bounce,
        public SesComplaintData|Optional $complaint,
        public SesDeliveryData|Optional $delivery,
        public SesSendData|Optional $send,
        public SesRejectData|Optional $reject,
        public SesOpenData|Optional $open,
        public SesClickData|Optional $click,
        public SesRenderingFailureData|Optional $renderingFailure,
        public SesDeliveryDelayData|Optional $deliveryDelay,
        public SesSubscriptionData|Optional $subscription,
    ) {}

    public static function fromRequest(Request $request)
    {
        $data = json_decode(json_decode($request->getContent(), true)['Message'], true);

        return new self(
            eventType: $data['eventType'],
            mail: SesMailData::from($data['mail']),
            bounce: isset($data['bounce']) ? SesBounceData::from($data['bounce']) : Optional::create(),
            complaint: isset($data['complaint']) ? SesComplaintData::from($data['complaint']) : Optional::create(),
            delivery: isset($data['delivery']) ? SesDeliveryData::from($data['delivery']) : Optional::create(),
            send: isset($data['send']) ? SesSendData::from($data['send']) : Optional::create(),
            reject: isset($data['reject']) ? SesRejectData::from($data['reject']) : Optional::create(),
            open: isset($data['open']) ? SesOpenData::from($data['open']) : Optional::create(),
            click: isset($data['click']) ? SesClickData::from($data['click']) : Optional::create(),
            renderingFailure: isset($data['renderingFailure']) ? SesRenderingFailureData::from($data['renderingFailure']) : Optional::create(),
            deliveryDelay: isset($data['deliveryDelay']) ? SesDeliveryDelayData::from($data['deliveryDelay']) : Optional::create(),
            subscription: isset($data['subscription']) ? SesSubscriptionData::from($data['subscription']) : Optional::create(),
        );
    }

    public function errorMessageFromType(): ?string
    {
        return match ($this->eventType) {
            'Bounce' => 'The email was not successfully delivered due to a permanent rejection from the recipient mail server.',
            'Delivery' => 'The email was successfully delivered.',
            'DeliveryDelay' => 'The email was not successfully delivered due to a temporary issue.',
            'Reject' => 'The email was not attempted to be delivered due to unsafe contents.',
            'RenderingFailure' => 'The email not successfully delivered due to a template rendering error.',
            default => null,
        };
    }
}
