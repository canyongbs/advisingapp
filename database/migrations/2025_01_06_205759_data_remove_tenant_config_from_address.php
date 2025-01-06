<?php

use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $tenant = Tenant::current();

        if (isset($tenant->config?->mail?->fromAddress)) {
            $config = $tenant->config;

            unset($config->mail->fromAddress);

            $tenant->config = $config;

            $tenant->save();
        }
    }

    public function down(): void
    {
        // Not needed
    }
};
