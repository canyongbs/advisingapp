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

namespace AdvisingApp\MeetingCenter\Actions;

use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use Illuminate\Support\Facades\DB;

class CreateEventRegistrationFormVersion
{
    /** @param array<string, mixed> $newData */
    public function execute(EventRegistrationForm $oldVersion, array $newData): EventRegistrationForm
    {
        return DB::transaction(function () use ($oldVersion, $newData) {
            $oldVersion->archive();

            $newVersion = new EventRegistrationForm();
            $newVersion->embed_enabled = $oldVersion->embed_enabled;
            $newVersion->allowed_domains = $oldVersion->allowed_domains;
            $newVersion->primary_color = $oldVersion->primary_color;
            $newVersion->rounding = $oldVersion->rounding;
            $newVersion->recaptcha_enabled = $oldVersion->recaptcha_enabled;
            $newVersion->is_wizard = $oldVersion->is_wizard;
            $newVersion->fill($newData);
            $newVersion->root_id = $oldVersion->root_id;
            $newVersion->event_id = $oldVersion->event_id;
            $newVersion->save();

            return $newVersion;
        });
    }
}
