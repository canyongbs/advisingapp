<?php

use App\Features\SubmitAiChatOnEnterFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        SubmitAiChatOnEnterFlag::activate();
    }

    public function down(): void
    {
        SubmitAiChatOnEnterFlag::deactivate();
    }
};
