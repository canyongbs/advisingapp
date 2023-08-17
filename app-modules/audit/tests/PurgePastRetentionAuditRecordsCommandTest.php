<?php

use Assist\Audit\Models\Audit;
use Assist\Audit\Settings\AuditSettings;

test('PurgePastRetentionAuditRecordsCommand properly deletes records', function () {
    $auditSettings = resolve(AuditSettings::class);
    $auditSettings->retention_duration = 90;
    $auditSettings->save();

    $retainedAudits = Audit::factory()
        ->count(5)
        ->create(
            [
                'created_at' => now()->subDays(30),
            ]
        );

    $purgedAudits = Audit::factory()
        ->count(5)
        ->create(
            [
                'created_at' => now()->subDays(100),
            ]
        );

    $auditCount = Audit::count();

    $this->artisan('audit:purge-past-retention-audit-records');

    expect(Audit::count())->toBe($auditCount - $purgedAudits->count());

    $retainedAudits->each(
        function ($audit) {
            expect(Audit::where('id', $audit->id)->exists())->toBeTrue();
        }
    );

    $purgedAudits->each(
        function ($audit) {
            expect(Audit::where('id', $audit->id)->exists())->toBeFalse();
        }
    );
});
