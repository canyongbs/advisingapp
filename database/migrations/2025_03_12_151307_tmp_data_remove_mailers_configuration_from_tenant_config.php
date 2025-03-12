<?php

use App\Models\Tenant;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $tenant = Tenant::current();

        /** @var TenantConfig $config */
        $config = $tenant->config;

        unset($config->mail->mailer, $config->mail->mailers);

        $tenant->config = $config;

        $tenant->save();
    }

    public function down(): void
    {
        $tenant = Tenant::current();

        /** @var TenantConfig $config */
        $config = $tenant->config;

        $config->mail['mailer'] = config('mail.default');
        $config->mail['mailers'] = [
            'smtp' => [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username'),
                'password' => config('mail.mailers.smtp.password'),
                'timeout' => config('mail.mailers.smtp.timeout'),
                'local_domain' => config('mail.mailers.smtp.local_domain'),
            ],
        ];

        $tenant->config = $config;

        $tenant->save();
    }
};
