<?php

use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;
use AdvisingApp\Assistant\Enums\AiAssistantType;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('ai_assistants')
            ->where('type', AiAssistantType::Default)
            ->update([
                'name' => 'Institutional Assistant',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $tenant = Tenant::current();

        DB::table('ai_assistants')
            ->where('type', AiAssistantType::Default)
            ->update([
                'name' => "{$tenant->name} AI Assistant",
                'updated_at' => now(),
            ]);
    }
};
