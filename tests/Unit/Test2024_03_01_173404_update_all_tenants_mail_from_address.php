<?php

use App\Models\Tenant;

use function Pest\Laravel\artisan;

use Database\Factories\TenantFactory;

it('changes tenant mail from address if it is hello@example.com', function () {
    $tenant = Tenant::factory()
        ->createQuietly(
            [
                'config' => tap(TenantFactory::defaultConfig(), fn ($config) => $config->mail->fromAddress = 'hello@example.com'),
            ]
        );

    expect($tenant->config->mail->fromAddress)->toBe('hello@example.com');

    artisan('operations:process landlord 2024_03_01_173404_update_all_tenants_mail_from_address --test');

    expect($tenant->fresh()->config->mail->fromAddress)->toBe('no-reply@advising.app');
});

it('will not change tenant mail from address if it is not hello@example.com', function () {
    $tenant = Tenant::factory()
        ->createQuietly(
            [
                'config' => tap(TenantFactory::defaultConfig(), fn ($config) => $config->mail->fromAddress = fake()->email()),
            ]
        );

    $email = $tenant->config->mail->fromAddress;

    expect($tenant->config->mail->fromAddress)->not()->toBe('hello@example.com');

    artisan('operations:process landlord 2024_03_01_173404_update_all_tenants_mail_from_address --test');

    expect($tenant->fresh()->config->mail->fromAddress)->toBe($email);
});
