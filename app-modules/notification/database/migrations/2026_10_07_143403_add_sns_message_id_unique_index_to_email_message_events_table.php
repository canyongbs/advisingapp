<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    // CREATE INDEX CONCURRENTLY cannot run inside a transaction block.
    public $withinTransaction = false;

    // @phpstan-ignore Common.multipleMigrationChangesNotWrappedInTransaction (CREATE INDEX CONCURRENTLY cannot run inside a transaction)
    public function up(): void
    {
        // A failed concurrent build leaves an invalid index behind, so a retry must drop it before building again.
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS email_message_events_sns_message_id_unique');

        // Every delivery of one SNS message (HTTP and SQS during the cutover, SQS redelivery, SNS retries) carries the same id.
        // Built concurrently so the events table is not write-locked, and partial so rows without an id stay out of it.
        DB::statement('CREATE UNIQUE INDEX CONCURRENTLY email_message_events_sns_message_id_unique ON email_message_events (sns_message_id) WHERE sns_message_id IS NOT NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX CONCURRENTLY email_message_events_sns_message_id_unique');
    }
};
