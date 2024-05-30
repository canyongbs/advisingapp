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
