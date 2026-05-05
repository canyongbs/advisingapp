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

namespace AdvisingApp\Application\Observers;

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Events\ApplicationSubmissionCreated;
use AdvisingApp\Application\Events\ApplicationSubmissionStateEntered;
use AdvisingApp\Application\Events\ApplicationSubmissionStateExited;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class ApplicationSubmissionObserver
{
    public function creating(ApplicationSubmission $submission): void
    {
        // @phpstan-ignore method.notFound
        $defaultState = ApplicationSubmissionState::query()
            ->withoutArchived()
            ->where('classification', ApplicationSubmissionStateClassification::Received)
            ->oldest('id')
            ->first();

        if (! $defaultState) {
            // @phpstan-ignore method.notFound
            $defaultState = ApplicationSubmissionState::query()
                ->withoutArchived()
                ->oldest('id')
                ->firstOrFail();
        }

        $submission->state()->associate(
            $defaultState
        );
    }

    public function created(ApplicationSubmission $submission): void
    {
        Event::dispatch(
            event: new ApplicationSubmissionCreated(submission: $submission)
        );

        $submission->loadMissing('state');

        Event::dispatch(
            event: new ApplicationSubmissionStateEntered(
                submission: $submission,
                state: $submission->state,
            )
        );

        if (! is_null($submission->author)) {
            Cache::tags('{application-submission-count}')
                ->forget(
                    "application-submission-count-{$submission->author->getKey()}"
                );
        }
    }

    public function updated(ApplicationSubmission $submission): void
    {
        if (! $submission->wasChanged('state_id')) {
            return;
        }

        $previousStateId = $submission->getOriginal('state_id');

        if ($previousStateId) {
            $previousState = ApplicationSubmissionState::withTrashed()->find($previousStateId);

            if ($previousState) {
                Event::dispatch(
                    event: new ApplicationSubmissionStateExited(
                        submission: $submission,
                        state: $previousState,
                    )
                );
            }
        }

        $submission->load('state');

        Event::dispatch(
            event: new ApplicationSubmissionStateEntered(
                submission: $submission,
                state: $submission->state,
            )
        );
    }
}
