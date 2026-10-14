<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Support\Sentry\ForgetUnsampledTraceDecision;

use function Sentry\continueTrace;

$traceId = '771a43a4192642f0b136d5159a501700';
$spanId = '8a3e9c0f5b2d4a1e';

it('lets a job queued under an unsampled request get its own sampling decision', function () use ($traceId, $spanId) {
    $payload = [
        'sentry_trace_parent_data' => "{$traceId}-{$spanId}-0",
        'sentry_baggage_data' => "sentry-trace_id={$traceId},sentry-sample_rate=0.05,sentry-sampled=false,sentry-sample_rand=0.42",
    ];

    expect(continueTrace($payload['sentry_trace_parent_data'], $payload['sentry_baggage_data'])->getParentSampled())->toBeFalse();

    $changes = (new ForgetUnsampledTraceDecision())('sqs', 'default', $payload);

    $context = continueTrace($changes['sentry_trace_parent_data'], $changes['sentry_baggage_data']);

    expect($changes)->toBe([
        'sentry_trace_parent_data' => "{$traceId}-{$spanId}",
        'sentry_baggage_data' => "sentry-trace_id={$traceId},sentry-sample_rate=0.05,sentry-sample_rand=0.42",
    ])
        ->and($context->getParentSampled())->toBeNull()
        ->and((string) $context->getTraceId())->toBe($traceId);
});

it('leaves the trace alone when the request was sampled or had not decided', function (string $traceParent) {
    expect((new ForgetUnsampledTraceDecision())('sqs', 'default', [
        'sentry_trace_parent_data' => $traceParent,
        'sentry_baggage_data' => 'sentry-sampled=true',
    ]))->toBe([]);
})->with([
    'sampled' => ['771a43a4192642f0b136d5159a501700-8a3e9c0f5b2d4a1e-1'],
    'undecided' => ['771a43a4192642f0b136d5159a501700-8a3e9c0f5b2d4a1e'],
]);

it('leaves payloads without Sentry trace data alone', function () {
    expect((new ForgetUnsampledTraceDecision())('sqs', 'default', ['job' => 'Example']))->toBe([])
        ->and((new ForgetUnsampledTraceDecision())('sqs', 'default', null))->toBe([]);
});
