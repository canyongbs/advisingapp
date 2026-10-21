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

namespace AdvisingApp\Form\Observers;

use AdvisingApp\Form\Events\FormSubmissionCreated;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class FormSubmissionObserver
{
    /**
     * A pending request has no `submitted_at` yet, so it has not actually been submitted and
     * `FormSubmissionCreated` is deferred until it is (see `updated()`). The form's submission
     * count includes pending requests, so its cache is cleared here instead of by the
     * `ClearFormFormSubmissionCountCache` listener.
     */
    public function created(FormSubmission $submission): void
    {
        if ($submission->submitted_at) {
            Event::dispatch(
                event: new FormSubmissionCreated(submission: $submission)
            );

            return;
        }

        $submission->loadMissing('submissible');

        if ($submission->submissible instanceof Form) {
            Cache::tags('{form-submission-count}')
                ->forget(
                    "form-submission-count-{$submission->submissible->root_id}"
                );
        }
    }

    public function updated(FormSubmission $submission): void
    {
        if ($submission->wasChanged('submitted_at') && $submission->submitted_at) {
            Event::dispatch(
                event: new FormSubmissionCreated(submission: $submission)
            );
        }
    }
}
