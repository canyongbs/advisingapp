<?php

use Assist\Case\Models\ServiceRequest;
use Assist\Audit\Settings\AuditSettings;

use function Tests\asSuperAdmin;

test('Audit logs are only created if the Model is set to be Audited by audit settings', function () {
    asSuperAdmin();

    $auditSettings = resolve(AuditSettings::class);

    $auditSettings->audited_models = [];

    $auditSettings->save();

    $case = ServiceRequest::factory()->create();

    expect($case->audits)->toHaveCount(0);

    $auditSettings->audited_models[] = $case->getMorphClass();

    $auditSettings->save();

    $case = ServiceRequest::factory()->create();

    expect($case->audits)->toHaveCount(1);
});
