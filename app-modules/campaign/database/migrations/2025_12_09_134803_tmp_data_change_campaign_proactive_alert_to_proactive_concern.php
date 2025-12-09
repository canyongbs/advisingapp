<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function() {
            DB::table('campaign_actions')
                ->where('type', 'proactive_alert')
                ->chunkById(100, function(Collection $campaignActions) {
                    foreach($campaignActions as $campaignAction) {
                        DB::table('campaign_actions')
                            ->where('id', $campaignAction->id)
                            ->update(['type' => 'proactive_concern']);
                    }
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function() {
            DB::table('campaign_actions')
                ->where('type', 'proactive_concern')
                ->chunkById(100, function(Collection $campaignActions) {
                    foreach($campaignActions as $campaignAction) {
                        DB::table('campaign_actions')
                            ->where('id', $campaignAction->id)
                            ->update(['type' => 'proactive_alert']);
                    }
            });
        });
    }
};
