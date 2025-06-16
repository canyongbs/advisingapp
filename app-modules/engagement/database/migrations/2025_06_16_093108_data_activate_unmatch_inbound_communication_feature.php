<?php

use App\Features\UnMatchInboundCommunicationFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        UnMatchInboundCommunicationFeature::activate();
    }

    public function down(): void
    {
        UnMatchInboundCommunicationFeature::deactivate();
    }
};
