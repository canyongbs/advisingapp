<?php

namespace AdvisingApp\IntegrationTwilio\Jobs;

use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Twilio\Exceptions\TwilioException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Notification\Models\OutboundDeliverable;

class CheckSmsOutboundDeliverableStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public OutboundDeliverable $deliverable,
    ) {}

    public function handle(Client $twilioClient): void
    {
        try {
            $messageInstance = $twilioClient->messages($this->deliverable->external_reference_id)->fetch();

            $this->deliverable->update([
                'external_status' => $messageInstance->status,
            ]);

            if ($this->deliverable->related && $this->deliverable->related instanceof EngagementDeliverable) {
                $this->deliverable->related->update([
                    'external_status' => $messageInstance->status,
                ]);

                match ($messageInstance->status) {
                    'delivered' => $this->deliverable->related->markDeliverySuccessful(Carbon::parse($messageInstance->dateSent)),
                    'undelivered', 'failed' => $this->deliverable->related->markDeliveryFailed($messageInstance->errorMessage ?? 'Message could not successfully be delivered.'),
                    default => null,
                };
            }

            match ($messageInstance->status) {
                'delivered' => $this->deliverable->markDeliverySuccessful(Carbon::parse($messageInstance->dateSent)),
                'undelivered', 'failed' => $this->deliverable->markDeliveryFailed($messageInstance->errorMessage ?? 'Message could not successfully be delivered.'),
                default => null,
            };
        } catch (TwilioException $e) {
            report($e);
        }
    }
}
