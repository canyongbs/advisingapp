<?php

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    public function up(): void
    {
        $this->renamePermissionGroups([
            'Knowledge Base Article' => 'Resource Hub Article',
        ]);
    }

    public function down(): void
    {
        $this->renamePermissionGroups([
            'Resource Hub Article' => 'Knowledge Base Article',
        ]);
    }
};
