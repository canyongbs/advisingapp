<?php

use Assist\Case\Models\CaseItem;

use function Tests\asSuperAdmin;

use Assist\Audit\Settings\AuditSettings;

test('Audit logs are only created if the Model is set to be Audited by audit settings', function () {
    asSuperAdmin();

    $auditSettings = resolve(AuditSettings::class);

    $auditSettings->audited_models = [];

    $auditSettings->save();

    $case = CaseItem::factory()->create();

    expect($case->audits)->toHaveCount(0);

    $auditSettings->audited_models[] = $case->getMorphClass();

    $auditSettings->save();

    $case = CaseItem::factory()->create();

    expect($case->audits)->toHaveCount(1);
});
