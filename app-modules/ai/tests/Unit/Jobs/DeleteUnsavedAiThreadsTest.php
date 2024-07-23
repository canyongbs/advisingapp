<?php

use AdvisingApp\Ai\Models\AiThread;
use App\Models\Tenant;

use function Pest\Laravel\artisan;
use function PHPUnit\Framework\assertNull;

it('selects and soft deletes the proper records', function() {
    $notSavedAndOlderThanThreeDays = AiThread::factory()->create(['saved_at' => null, 'created_at' => now()->subDays(4)]);
    $notSavedAndEarlierThanThreeDays = AiThread::factory()->create(['saved_at' => null, 'created_at' => now()->subDays(2)]);
    $savedAndOlderThanThreeDays = AiThread::factory()->create(['saved_at' => now(), 'created_at' => now()->subDays(4)]);
    $savedAndEarlierThanThreeDays = AiThread::factory()->create(['saved_at' => now(), 'created_at' => now()->subDays(2)]);

    $tenant = Tenant::current();

    artisan("ai:delete-unsaved-ai-threads --tenant={$tenant->getKey()}");

    expect($notSavedAndOlderThanThreeDays->fresh()->deleted_at)->not->toBeNull()
        ->and($notSavedAndEarlierThanThreeDays->fresh()->deleted_at)->toBeNull()
        ->and($savedAndOlderThanThreeDays->fresh()->deleted_at)->toBeNull()
        ->and($savedAndEarlierThanThreeDays->fresh()->deleted_at)->toBeNull();
});
