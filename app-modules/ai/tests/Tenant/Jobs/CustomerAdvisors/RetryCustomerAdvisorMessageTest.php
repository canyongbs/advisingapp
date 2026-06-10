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

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Events\CustomerAdvisors\CustomerAdvisorMessageChunk;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\RetryCustomerAdvisorMessage;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorMessage;
use AdvisingApp\Ai\Models\CustomerAdvisorThread;
use AdvisingApp\Ai\Services\TestAiService;
use AdvisingApp\Ai\Support\StreamingChunks\Finish;
use AdvisingApp\Ai\Support\StreamingChunks\Text;
use Illuminate\Support\Facades\Event;

it('reuses the existing user message when retrying instead of duplicating it', function () {
    Event::fake([CustomerAdvisorMessageChunk::class]);

    $advisor = CustomerAdvisor::factory()->create(['model' => AiModel::Test]);
    $thread = CustomerAdvisorThread::factory()->for($advisor, 'advisor')->create();

    // Simulate a prior message whose advisor response failed (no advisor reply persisted).
    $userMessage = new CustomerAdvisorMessage();
    $userMessage->thread()->associate($thread);
    $userMessage->content = 'What are your office hours?';
    $userMessage->is_advisor = false;
    $userMessage->save();

    $service = Mockery::mock(TestAiService::class)->makePartial();
    $service->shouldReceive('streamRaw')->andReturn(function () {
        yield new Text('We are open 9 to 5.');

        yield new Finish();
    });
    app()->instance(TestAiService::class, $service);

    dispatch_sync(new RetryCustomerAdvisorMessage($advisor, $thread, 'What are your office hours?'));

    // The user message is reused (not duplicated) and a fresh advisor response is generated.
    expect(CustomerAdvisorMessage::query()->where('is_advisor', false)->count())->toBe(1);

    $response = CustomerAdvisorMessage::query()->where('is_advisor', true)->first();

    expect($response)->not->toBeNull()
        ->and($response->content)->toBe('We are open 9 to 5.');
});
