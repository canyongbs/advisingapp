<?php

use App\Features\HolisticEngagementFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::createView(
                'holistic_engagements',
                <<<SQL
                    SELECT
                        'engagement' AS record_type,
                        id AS record_id,
                        'outbound' AS direction,
                        channel AS type,
                        recipient_type AS sent_to_type,
                        recipient_id::VARCHAR AS sent_to_id,
                        'user' AS sent_by_type,
                        user_id::VARCHAR AS sent_by_id,
                        recipient_type AS concern_type,
                        recipient_id::VARCHAR AS concern_id,
                        dispatched_at AS record_sortable_date,
                        created_at,
                        updated_at,
                        deleted_at
                    FROM engagements

                    UNION ALL

                    SELECT
                        'engagement_response' AS record_type,
                        id AS record_id,
                        'inbound' AS direction,
                        type AS type,
                        NULL AS sent_to_type,
                        NULL AS sent_to_id,
                        sender_type AS sent_by_type,
                        sender_id::VARCHAR AS sent_by_id,
                        sender_type AS concern_type,
                        sender_id::VARCHAR AS concern_id,
                        sent_at AS record_sortable_date,
                        created_at,
                        updated_at,
                        deleted_at
                    FROM engagement_responses;
                SQL,
            );

            HolisticEngagementFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            HolisticEngagementFeature::deactivate();

            Schema::dropView('holistic_engagements');
        });
    }
};
