<?php

use App\Features\CampaignEmailImages;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        CampaignEmailImages::activate();
    }

    public function down(): void
    {
        CampaignEmailImages::deactivate();
    }
};
