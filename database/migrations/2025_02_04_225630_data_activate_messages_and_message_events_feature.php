<?php

use App\Features\MessagesAndMessageEvents;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        MessagesAndMessageEvents::activate();
    }

    public function down(): void
    {
        MessagesAndMessageEvents::deactivate();
    }
};
