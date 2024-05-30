<?php

use App\Models\User;
use App\Models\Tenant;

use function Pest\Laravel\get;

use App\Filament\Pages\AmazonS3;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use App\Multitenancy\DataTransferObjects\TenantConfig;

it('prevents access to the Amazon S3 Settings when you do not have the necessary permissions', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(AmazonS3::getUrl())
        ->assertForbidden();
});

it('allows access to the Amazon S3 Settings when you do have the necessary permissions', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('amazon-s3.manage_s3_settings');

    actingAs($user);

    get(AmazonS3::getUrl())
        ->assertOk();
});

it('renders the correct Amazon S3 settings for the Tenant', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('amazon-s3.manage_s3_settings');

    actingAs($user);

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

    $user->givePermissionTo('amazon-s3.manage_s3_settings');

    actingAs($user);

    /** @var Tenant $tenant */
    $tenant = Tenant::current();

    /** @var TenantConfig $config */
    $config = $tenant->config;

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

    expect($tenant->config->s3Filesystem->key)->toBe('new-key');
    expect($tenant->config->s3Filesystem->secret)->toBe('new-secret');
    expect($tenant->config->s3Filesystem->region)->toBe('new-region');
    expect($tenant->config->s3Filesystem->bucket)->toBe('new-bucket');
    expect($tenant->config->s3Filesystem->url)->toBe('new-url');
    expect($tenant->config->s3Filesystem->endpoint)->toBe('new-endpoint');
    expect($tenant->config->s3Filesystem->usePathStyleEndpoint)->toBe(true);
    expect($tenant->config->s3Filesystem->throw)->toBe(true);
    expect($tenant->config->s3Filesystem->root)->toBe('new-root');
    expect($tenant->config->s3PublicFilesystem->key)->toBe('new-public-key');
    expect($tenant->config->s3PublicFilesystem->secret)->toBe('new-public-secret');
    expect($tenant->config->s3PublicFilesystem->region)->toBe('new-public-region');
    expect($tenant->config->s3PublicFilesystem->bucket)->toBe('new-public-bucket');
    expect($tenant->config->s3PublicFilesystem->url)->toBe('new-public-url');
    expect($tenant->config->s3PublicFilesystem->endpoint)->toBe('new-public-endpoint');
    expect($tenant->config->s3PublicFilesystem->usePathStyleEndpoint)->toBe(true);
    expect($tenant->config->s3PublicFilesystem->throw)->toBe(true);
    expect($tenant->config->s3PublicFilesystem->root)->toBe('new-public-root');
});
