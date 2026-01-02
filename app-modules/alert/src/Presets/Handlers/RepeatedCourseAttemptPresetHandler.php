<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Alert\Presets\Handlers;

use AdvisingApp\Alert\Contracts\AlertPresetConfiguration;
use AdvisingApp\Alert\Presets\Handlers\Contracts\AlertPresetHandler;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class RepeatedCourseAttemptPresetHandler implements AlertPresetHandler
{
    public function getName(): string
    {
        return 'Repeated Course Attempt';
    }

    public function getDescription(): string
    {
        return 'This alert is turned on when a student enrolls in the same course more than once. It is intended to flag students who previously struggled with a course and may benefit from additional preparation or academic support when attempting it again.';
    }

    public function configurationForm(): array
    {
        return [];
    }

    public function getConfigurationModel(): ?string
    {
        return null;
    }

    public function getStudentAlertQuery(?AlertPresetConfiguration $configuration): Builder
    {
        return DB::table(DB::raw('(SELECT sisid, class_nbr, COUNT(*) as attempt_count FROM enrollments WHERE deleted_at IS NULL GROUP BY sisid, class_nbr HAVING COUNT(*) > 1) as repeated_courses'))
            ->select('sisid')
            ->distinct();
    }
}
