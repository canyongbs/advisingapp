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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('outbound_deliverables')
            ->where('channel', 'email')
            ->chunkById(100, function (Collection $outboundDeliverables) {
                $outboundDeliverables->each(function (object $outboundDeliverable) {
                    $id = Str::orderedUuid();

                    DB::table('email_messages')
                        ->insert([
                            'id' => $id,
                            'notification_class' => $outboundDeliverable->notification_class,
                            'external_reference_id' => $outboundDeliverable->external_reference_id,
                            'content' => $outboundDeliverable->content,
                            'quota_usage' => $outboundDeliverable->quota_usage,
                            'related_type' => $outboundDeliverable->related_type,
                            'related_id' => $outboundDeliverable->related_id,
                            'recipient_type' => $outboundDeliverable->recipient_type,
                            'recipient_id' => $outboundDeliverable->recipient_id,
                            'created_at' => $outboundDeliverable->created_at,
                            'updated_at' => $outboundDeliverable->updated_at,
                        ]);

                    if ($outboundDeliverable->delivery_status === 'dispatched') {
                        DB::table('email_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'email_message_id' => $id,
                                'type' => 'dispatched',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'failed_dispatch') {
                        DB::table('email_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'email_message_id' => $id,
                                'type' => 'failed_dispatch',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'rate_limited') {
                        DB::table('email_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'email_message_id' => $id,
                                'type' => 'rate_limited',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'blocked_by_demo_mode') {
                        DB::table('email_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'email_message_id' => $id,
                                'type' => 'blocked_by_demo_mode',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'failed') {
                        DB::table('email_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'email_message_id' => $id,
                                'type' => 'dispatched',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);

                        DB::table('email_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'email_message_id' => $id,
                                'type' => 'reject',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'successful') {
                        DB::table('email_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'email_message_id' => $id,
                                'type' => 'dispatched',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);

                        DB::table('email_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'email_message_id' => $id,
                                'type' => 'delivery',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    }
                });
            });

        DB::table('outbound_deliverables')
            ->where('channel', 'sms')
            ->chunkById(100, function (Collection $outboundDeliverables) {
                $outboundDeliverables->each(function (object $outboundDeliverable) {
                    $id = Str::orderedUuid();

                    DB::table('sms_messages')
                        ->insert([
                            'id' => $id,
                            'notification_class' => $outboundDeliverable->notification_class,
                            'external_reference_id' => $outboundDeliverable->external_reference_id,
                            'content' => $outboundDeliverable->content,
                            'quota_usage' => $outboundDeliverable->quota_usage,
                            'related_type' => $outboundDeliverable->related_type,
                            'related_id' => $outboundDeliverable->related_id,
                            'recipient_type' => $outboundDeliverable->recipient_type,
                            'recipient_id' => $outboundDeliverable->recipient_id,
                            'created_at' => $outboundDeliverable->created_at,
                            'updated_at' => $outboundDeliverable->updated_at,
                        ]);

                    if ($outboundDeliverable->delivery_status === 'dispatched') {
                        DB::table('sms_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'sms_message_id' => $id,
                                'type' => 'dispatched',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'failed_dispatch') {
                        DB::table('sms_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'sms_message_id' => $id,
                                'type' => 'failed_dispatch',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'rate_limited') {
                        DB::table('sms_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'sms_message_id' => $id,
                                'type' => 'rate_limited',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'blocked_by_demo_mode') {
                        DB::table('sms_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'sms_message_id' => $id,
                                'type' => 'blocked_by_demo_mode',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'failed') {
                        DB::table('sms_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'sms_message_id' => $id,
                                'type' => 'dispatched',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);

                        DB::table('sms_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'sms_message_id' => $id,
                                'type' => 'failed',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    } elseif ($outboundDeliverable->delivery_status === 'successful') {
                        DB::table('sms_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'sms_message_id' => $id,
                                'type' => 'dispatched',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);

                        DB::table('sms_message_events')
                            ->insert([
                                'id' => Str::orderedUuid(),
                                'sms_message_id' => $id,
                                'type' => 'delivered',
                                'payload' => json_encode([]),
                                'occurred_at' => $outboundDeliverable->created_at,
                                'created_at' => $outboundDeliverable->created_at,
                                'updated_at' => $outboundDeliverable->created_at,
                            ]);
                    }
                });
            });

        DB::table('outbound_deliverables')
            ->where('channel', 'database')
            ->chunkById(100, function (Collection $outboundDeliverables) {
                $outboundDeliverables->each(function (object $outboundDeliverable) {
                    $id = Str::orderedUuid();

                    DB::table('database_messages')
                        ->insert([
                            'id' => $id,
                            'notification_class' => $outboundDeliverable->notification_class,
                            'content' => $outboundDeliverable->content,
                            'related_type' => $outboundDeliverable->related_type,
                            'related_id' => $outboundDeliverable->related_id,
                            'recipient_type' => $outboundDeliverable->recipient_type,
                            'recipient_id' => $outboundDeliverable->recipient_id,
                            'created_at' => $outboundDeliverable->created_at,
                            'updated_at' => $outboundDeliverable->updated_at,
                        ]);
                });
            });
    }

    public function down(): void
    {
        // There is no down possible for this
    }
};
