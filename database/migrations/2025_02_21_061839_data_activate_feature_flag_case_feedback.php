<?php

use App\Features\CaseFeedback;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        CaseFeedback::activate();
    }

    public function down(): void
    {
        CaseFeedback::deactivate();
    }
};
