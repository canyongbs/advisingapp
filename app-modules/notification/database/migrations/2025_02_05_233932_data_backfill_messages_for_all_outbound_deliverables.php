<?php

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
