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

namespace AdvisingApp\Application\Actions;

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use Illuminate\Support\Facades\DB;

class CreateApplicationVersion
{
    /** @param array<string, mixed> $newData */
    public function execute(Application $oldVersion, array $newData): Application
    {
        return DB::transaction(function () use ($oldVersion, $newData) {
            $oldVersion->archive();

            $newVersion = new Application();
            $newVersion->fill($newData);
            $newVersion->root_id = $oldVersion->root_id;
            $newVersion->notify_to_care_team = $oldVersion->notify_to_care_team;
            $newVersion->notify_to_subscribers = $oldVersion->notify_to_subscribers;
            $newVersion->notify_via_app = $oldVersion->notify_via_app;
            $newVersion->notify_via_email = $oldVersion->notify_via_email;
            $newVersion->save();

            $this->reassociateNotificationUsers($oldVersion, $newVersion);
            $this->reassociateWorkflowTriggers($oldVersion, $newVersion);

            return $newVersion;
        });
    }

    private function reassociateNotificationUsers(Application $oldVersion, Application $newVersion): void
    {
        $userIds = $oldVersion->notificationUsers()->allRelatedIds();
        $oldVersion->notificationUsers()->detach();
        $newVersion->notificationUsers()->attach($userIds);
    }

    private function reassociateWorkflowTriggers(Application $oldVersion, Application $newVersion): void
    {
        $oldVersion->workflowTriggers->each(function (WorkflowTrigger $trigger) use ($newVersion) {
            $trigger->related()->associate($newVersion);
            $trigger->save();
        });
    }
}
