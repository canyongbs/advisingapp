<?php

use Illuminate\Database\Migrations\Migration;
use App\Features\TwilioDemoAutoReplyModeFeature;

return new class () extends Migration {
    public function up(): void
    {
        TwilioDemoAutoReplyModeFeature::activate();
    }

    public function down(): void
    {
        TwilioDemoAutoReplyModeFeature::deactivate();
    }
};
