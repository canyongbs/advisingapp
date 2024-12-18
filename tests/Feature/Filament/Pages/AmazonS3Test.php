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

use App\Filament\Pages\AmazonS3;
use App\Models\Tenant;
use App\Models\User;
use App\Multitenancy\DataTransferObjects\TenantConfig;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('prevents access to the Amazon S3 Settings when you do not have the necessary permissions', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(AmazonS3::getUrl())
        ->assertForbidden();
});

it('allows access to the Amazon S3 Settings when you do have the necessary permissions', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    get(AmazonS3::getUrl())
        ->assertOk();
});

it('renders the correct Amazon S3 settings for the Tenant', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    /** @var Tenant $tenant */
    $tenant = Tenant::current();

    /** @var TenantConfig $config */
    $config = $tenant->config;

    livewire(AmazonS3::class)
        ->assertFormSet([
            's3.key' => $config->s3Filesystem->key,
            's3.secret' => $config->s3Filesystem->secret,
            's3.region' => $config->s3Filesystem->region,
            's3.bucket' => $config->s3Filesystem->bucket,
            's3.url' => $config->s3Filesystem->url,
            's3.endpoint' => $config->s3Filesystem->endpoint,
            's3.usePathStyleEndpoint' => $config->s3Filesystem->usePathStyleEndpoint,
            's3.throw' => $config->s3Filesystem->throw,
            's3.root' => $config->s3Filesystem->root,
            's3-public.key' => $config->s3PublicFilesystem->key,
            's3-public.secret' => $config->s3PublicFilesystem->secret,
            's3-public.region' => $config->s3PublicFilesystem->region,
            's3-public.bucket' => $config->s3PublicFilesystem->bucket,
            's3-public.url' => $config->s3PublicFilesystem->url,
            's3-public.endpoint' => $config->s3PublicFilesystem->endpoint,
            's3-public.usePathStyleEndpoint' => $config->s3PublicFilesystem->usePathStyleEndpoint,
            's3-public.throw' => $config->s3PublicFilesystem->throw,
            's3-public.root' => $config->s3PublicFilesystem->root,
        ]);
});

it('correctly edits the Amazon S3 settings for the Tenant', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    /** @var Tenant $tenant */
    $tenant = Tenant::current();

    livewire(AmazonS3::class)
        ->fillForm([
            's3.key' => 'new-key',
            's3.secret' => 'new-secret',
            's3.region' => 'new-region',
            's3.bucket' => 'new-bucket',
            's3.url' => 'new-url',
            's3.endpoint' => 'new-endpoint',
            's3.usePathStyleEndpoint' => true,
            's3.throw' => true,
            's3.root' => 'new-root',
            's3-public.key' => 'new-public-key',
            's3-public.secret' => 'new-public-secret',
            's3-public.region' => 'new-public-region',
            's3-public.bucket' => 'new-public-bucket',
            's3-public.url' => 'new-public-url',
            's3-public.endpoint' => 'new-public-endpoint',
            's3-public.usePathStyleEndpoint' => true,
            's3-public.throw' => true,
            's3-public.root' => 'new-public-root',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $tenant->refresh();

    expect($tenant->config->s3Filesystem->key)->toBe('new-key')
        ->and($tenant->config->s3Filesystem->secret)->toBe('new-secret')
        ->and($tenant->config->s3Filesystem->region)->toBe('new-region')
        ->and($tenant->config->s3Filesystem->bucket)->toBe('new-bucket')
        ->and($tenant->config->s3Filesystem->url)->toBe('new-url')
        ->and($tenant->config->s3Filesystem->endpoint)->toBe('new-endpoint')
        ->and($tenant->config->s3Filesystem->usePathStyleEndpoint)->toBe(true)
        ->and($tenant->config->s3Filesystem->throw)->toBe(true)
        ->and($tenant->config->s3Filesystem->root)->toBe('new-root')
        ->and($tenant->config->s3PublicFilesystem->key)->toBe('new-public-key')
        ->and($tenant->config->s3PublicFilesystem->secret)->toBe('new-public-secret')
        ->and($tenant->config->s3PublicFilesystem->region)->toBe('new-public-region')
        ->and($tenant->config->s3PublicFilesystem->bucket)->toBe('new-public-bucket')
        ->and($tenant->config->s3PublicFilesystem->url)->toBe('new-public-url')
        ->and($tenant->config->s3PublicFilesystem->endpoint)->toBe('new-public-endpoint')
        ->and($tenant->config->s3PublicFilesystem->usePathStyleEndpoint)->toBe(true)
        ->and($tenant->config->s3PublicFilesystem->throw)->toBe(true)
        ->and($tenant->config->s3PublicFilesystem->root)->toBe('new-public-root');
});
