<?php

use App\Features\CancelCampaignAction;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        CancelCampaignAction::activate();
    }

    public function down(): void
    {
        CancelCampaignAction::deactivate();
    }
};
