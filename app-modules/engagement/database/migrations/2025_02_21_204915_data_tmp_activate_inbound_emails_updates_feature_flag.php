<?php

use App\Features\InboundEmailsUpdates;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        InboundEmailsUpdates::activate();
    }

    public function down(): void
    {
        InboundEmailsUpdates::deactivate();
    }
};
