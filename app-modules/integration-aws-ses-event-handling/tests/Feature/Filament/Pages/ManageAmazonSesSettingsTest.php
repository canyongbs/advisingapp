<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
