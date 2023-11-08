<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

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
}
