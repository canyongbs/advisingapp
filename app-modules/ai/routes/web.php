<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Http\Controllers\Advisors\CompleteResponseController;
use AdvisingApp\Ai\Http\Controllers\Advisors\RetryMessageController;
use AdvisingApp\Ai\Http\Controllers\Advisors\SendMessageController;
use AdvisingApp\Ai\Http\Controllers\Advisors\ShowThreadController;
use AdvisingApp\Ai\Http\Controllers\QnaAdvisors\PreviewAdvisorEmbedController;
use AdvisingApp\Ai\Http\Controllers\QnaAdvisors\SendAdvisorMessageController as SendQnaAdvisorMessageController;
use AdvisingApp\Ai\Http\Controllers\QnaAdvisors\ShowAdvisorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->name('ai.')
    ->group(function () {
        Route::get('ai/advisors/threads/{thread}', ShowThreadController::class)
            ->name('advisors.threads.show');

        Route::post('ai/advisors/threads/{thread}/messages', SendMessageController::class)
            ->name('advisors.threads.messages.send');

        Route::post('ai/advisors/threads/{thread}/messages/retry', RetryMessageController::class)
            ->name('advisors.threads.messages.retry');

        Route::post('ai/advisors/threads/{thread}/messages/complete-response', CompleteResponseController::class)
            ->name('advisors.threads.messages.complete-response');

        Route::get('ai/qna-advisors/{advisor}/preview-embed', PreviewAdvisorEmbedController::class)
            ->name('qna-advisors.preview-embed');
    });

Route::middleware(['web', 'signed:relative'])
    ->name('ai.qna-advisors.')
    ->prefix('api/ai/qna-advisors/{advisor}')
    ->group(function () {
        Route::get('/', ShowAdvisorController::class)
            ->name('show');

        Route::post('messages', SendQnaAdvisorMessageController::class)
            ->name('messages.send');
    });
