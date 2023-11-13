<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

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
}
