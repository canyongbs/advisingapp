<?php

use App\Features\RefactorEngagementCampaignSubjectToJsonb;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::statement('
                UPDATE engagements
                SET subject = (
                    \'{"type":"doc","content":[{"type":"paragraph","attrs":{"class":null,"style":null},"content":[{"type":"text","text":"\' || REPLACE(subject, \'"\', \'\\\"\') || \'"}]}]}\'
                )::jsonb
            ');

            DB::statement('
                UPDATE engagement_batches
                SET subject = (
                    \'{"type":"doc","content":[{"type":"paragraph","attrs":{"class":null,"style":null},"content":[{"type":"text","text":"\' || REPLACE(subject, \'"\', \'\\\"\') || \'"}]}]}\'
                )::jsonb
            ');

            DB::statement("
                UPDATE campaign_actions
                SET data = (
                    '{\"channel\":\"' || (data::json ->> 'channel') || '\",' ||
                    '\"subject\":' ||
                    '{\"type\":\"doc\",\"content\":[{\"type\":\"paragraph\",\"attrs\":{\"class\":null,\"style\":null},\"content\":[{\"type\":\"text\",\"text\":\"' || REPLACE(data::json ->> 'subject', '\"', '\\\"') || '\"}]}]},' ||
                    '\"body\":' || (data::json -> 'body')::text ||
                    '}'
                )::json
                WHERE json_typeof(data::json -> 'subject') = 'string'
            ");

            RefactorEngagementCampaignSubjectToJsonb::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::statement('
                UPDATE engagements
                SET subject = subject::jsonb->\'content\'->0->\'content\'->0->>\'text\'
            ');

            DB::statement('
                UPDATE engagement_batches
                SET subject = subject::jsonb->\'content\'->0->\'content\'->0->>\'text\'
            ');

            DB::statement("
                UPDATE campaign_actions
                SET data = (
                    '{\"channel\":\"' || (data::json ->> 'channel') || '\",' ||
                    '\"subject\":\"' || (data::json -> 'subject' -> 'content' -> 0 -> 'content' -> 0 ->> 'text') || '\",' ||
                    '\"body\":' || (data::json -> 'body')::text ||
                    '}'
                )::json
                WHERE json_typeof(data::json -> 'subject') = 'object'
                AND (data::json -> 'subject' ->> 'type') = 'doc'
            ");

            RefactorEngagementCampaignSubjectToJsonb::deactivate();
        });
    }
};
