<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    // Each batch commits separately so the events table is never held in one long transaction.
    public $withinTransaction = false;

    public function up(): void
    {
        DB::table('email_message_events')->select('id')->chunkById(5000, function (Collection $events) {
            $ids = $events->pluck('id');

            // SES reports each event's time in the event-specific object; the mail object carries the send time.
            // Values are ISO 8601 in UTC, and occurred_at stores UTC wall time without a zone.
            DB::update(<<<'SQL'
                with timestamps as (
                    select id, case type
                        when 'send' then payload #>> '{mail,timestamp}'
                        when 'reject' then payload #>> '{mail,timestamp}'
                        when 'rendering_failure' then payload #>> '{mail,timestamp}'
                        when 'bounce' then payload #>> '{bounce,timestamp}'
                        when 'complaint' then payload #>> '{complaint,timestamp}'
                        when 'delivery' then payload #>> '{delivery,timestamp}'
                        when 'delivery_delay' then payload #>> '{deliveryDelay,timestamp}'
                        when 'open' then payload #>> '{open,timestamp}'
                        when 'click' then payload #>> '{click,timestamp}'
                        when 'subscription' then payload #>> '{subscription,timestamp}'
                    end as value
                    from email_message_events
                    where id between ? and ?
                )
                update email_message_events
                set occurred_at = timestamps.value::timestamptz at time zone 'UTC'
                from timestamps
                where email_message_events.id = timestamps.id
                    and timestamps.value ~ ?
                SQL, [
                $ids->first(),
                $ids->last(),
                '^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:?\d{2})$',
            ]);
        });
    }

    public function down(): void {}
};
