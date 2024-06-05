<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifyPermissions;

return new class () extends Migration {
    use CanModifyPermissions;

    public function up(): void
    {
        $this->createPermissions(
            [
                'ai.view_cognitive_services_settings' => 'Integration: Cognitive Services',
            ],
            guardName: 'web'
        );
    }

    public function down(): void
    {
        $this->deletePermissions(
            [
                'ai.view_cognitive_services_settings',
            ],
            guardName: 'web',
        );
    }
};
