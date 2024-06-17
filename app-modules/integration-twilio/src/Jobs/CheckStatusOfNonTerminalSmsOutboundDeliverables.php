<?php

namespace AdvisingApp\IntegrationTwilio\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Notification\Models\OutboundDeliverable;

class CheckStatusOfNonTerminalSmsOutboundDeliverables implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public static $OLDER_THAN_HOURS = 24; // 1 day

    public static $NEWER_THAN_HOURS = 168; // 7 days

    public function __construct() {}

    public function handle(): void
    {
        $terminalStatuses = ['delivered', 'undelivered', 'failed'];

        OutboundDeliverable::query()
            ->where('channel', 'sms')
            ->whereNotIn('external_status', $terminalStatuses)
            ->whereNotNull('last_delivery_attempt')
            ->whereBetween('last_delivery_attempt', [now()->subHours(self::$NEWER_THAN_HOURS), now()->subHours(self::$OLDER_THAN_HOURS)])
            ->cursor()
            ->each(function (OutboundDeliverable $deliverable) {
                CheckSmsOutboundDeliverableStatus::dispatch($deliverable);
            });
    }
}
