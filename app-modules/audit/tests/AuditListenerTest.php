<?php

use function Tests\asSuperAdmin;

use Assist\Audit\Settings\AuditSettings;
use Assist\ServiceManagement\Models\ServiceRequest;

test('Audit logs are only created if the Model is set to be Audited by audit settings', function () {
    asSuperAdmin();

    $auditSettings = resolve(AuditSettings::class);

    $auditSettings->audited_models = [];

    $auditSettings->save();

    $serviceRequest = ServiceRequest::factory()->create();

    expect($serviceRequest->audits)->toHaveCount(0);

    $auditSettings->audited_models[] = $serviceRequest->getMorphClass();

    $auditSettings->save();

    $serviceRequest = ServiceRequest::factory()->create();

    expect($serviceRequest->audits)->toHaveCount(1);
});
