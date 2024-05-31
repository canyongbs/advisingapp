<?php

use App\Models\User;
use App\Models\Tenant;

use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\IntegrationAwsSesEventHandling\Filament\Pages\ManageAmazonSesSettings;

it('prevents access to the Amazon SES Settings when you do not have the necessary permissions', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ManageAmazonSesSettings::getUrl())
        ->assertForbidden();
});

it('allows access to the Amazon SES Settings when you do have the necessary permissions', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('integration-aws-ses-event-handling.view_ses_settings');

    actingAs($user);

    get(ManageAmazonSesSettings::getUrl())
        ->assertOk();
});

it('renders the correct Amazon SES settings for the Tenant', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('integration-aws-ses-event-handling.view_ses_settings');

    actingAs($user);

    /** @var Tenant $tenant */
    $tenant = Tenant::current();

    /** @var TenantConfig $config */
    $config = $tenant->config;

    livewire(ManageAmazonSesSettings::class)
        ->assertFormSet([
            'fromAddress' => $config->mail->fromAddress,
            'fromName' => $config->mail->fromName,
            'smtp_host' => $config->mail->mailers->smtp->host,
            'smtp_port' => $config->mail->mailers->smtp->port,
            'smtp_encryption' => $config->mail->mailers->smtp->encryption,
            'smtp_username' => $config->mail->mailers->smtp->username,
            'smtp_password' => $config->mail->mailers->smtp->password,
            'smtp_timeout' => $config->mail->mailers->smtp->timeout,
            'smtp_local_domain' => $config->mail->mailers->smtp->localDomain,
        ]);
});

it('correctly edits the Amazon SES settings for the Tenant', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('integration-aws-ses-event-handling.view_ses_settings');

    actingAs($user);

    /** @var Tenant $tenant */
    $tenant = Tenant::current();

    /** @var TenantConfig $config */
    $config = $tenant->config;

    livewire(ManageAmazonSesSettings::class)
        ->fillForm(
            [
                'fromAddress' => 'new@test.com',
                'fromName' => 'new-from-name',
                'smtp_host' => 'new-smtp-host',
                'smtp_port' => 123,
                'smtp_encryption' => 'new-smtp-encryption',
                'smtp_username' => 'new-smtp-username',
                'smtp_password' => 'new-smtp-password',
                'smtp_timeout' => 456,
                'smtp_local_domain' => 'new-smtp-local-domain',
            ]
        )
        ->call('save')
        ->assertHasNoFormErrors();

    $tenant->refresh();

    expect($tenant->config->mail->fromAddress)->toBe('new@test.com')
        ->and($tenant->config->mail->fromName)->toBe('new-from-name')
        ->and($tenant->config->mail->mailers->smtp->host)->toBe('new-smtp-host')
        ->and($tenant->config->mail->mailers->smtp->port)->toBe(123)
        ->and($tenant->config->mail->mailers->smtp->encryption)->toBe('new-smtp-encryption')
        ->and($tenant->config->mail->mailers->smtp->username)->toBe('new-smtp-username')
        ->and($tenant->config->mail->mailers->smtp->password)->toBe('new-smtp-password')
        ->and($tenant->config->mail->mailers->smtp->timeout)->toBe(456)
        ->and($tenant->config->mail->mailers->smtp->localDomain)->toBe('new-smtp-local-domain');
});
