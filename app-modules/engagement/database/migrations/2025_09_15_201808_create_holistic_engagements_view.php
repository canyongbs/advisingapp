<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::createView(
            'holistic_engagements',
            <<<SQL
                SELECT
                    'engagement' AS record_type,
                    id AS record_id,
                    recipient_type AS concern_type,
                    recipient_id AS concern_id,
                    dispatched_at AS record_sortable_date,
                    created_at,
                    updated_at,
                    deleted_at
                FROM engagements

                UNION ALL

                SELECT
                    'engagement_response' AS record_type,
                    id AS record_id,
                    sender_type AS concern_type,
                    sender_id AS concern_id,
                    sent_at AS record_sortable_date,
                    created_at,
                    updated_at,
                    deleted_at
                FROM engagement_responses;
            SQL,
        );
    }

    public function down(): void
    {
        Schema::dropView('holistic_engagements');
    }
};
