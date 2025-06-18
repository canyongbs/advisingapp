<?php

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Jobs\ProcessSesS3InboundEmail;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

it('can logs unmatched inbound communication if no student or prospect is found', function () {
    Storage::fake('s3-inbound-email');

    $emailFilePath = 'emails/test-email.eml';

    $emailContent = <<<EOD
        Subject: Test Email
        Date: Mon, 16 Jun 2025 12:00:00 +0000
        From: unknown@example.com
        To: tenant-slug@example.com
        X-SES-Spam-Verdict: PASS
        X-SES-Virus-Verdict: PASS

        <html><body>This is a test email body.</body></html>
        EOD;

    Storage::disk('s3-inbound-email')->put($emailFilePath, $emailContent);

    Tenant::factory()->create(['domain' => 'tenant-slug.example.com']);

    $job = new ProcessSesS3InboundEmail($emailFilePath);
    $job->handle();

    expect(DB::table('unmatched_inbound_communications')
        ->where([
            'sender' => 'unknown@example.com',
            'subject' => 'Test Email',
            'type' => EngagementResponseType::Email,
        ])
        ->count())->toBeGreaterThan(0);
});
