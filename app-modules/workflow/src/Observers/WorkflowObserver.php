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

namespace AdvisingApp\Workflow\Observers;

use AdvisingApp\Workflow\Models\Workflow;
use App\Features\WorkflowNameCitextFeature;

class WorkflowObserver
{
    /**
     * Keep the denormalized owner (related_type/related_id) in sync with the workflow's
     * trigger on creation. These columns exist only to scope the case-insensitive unique
     * index on `name` to the owning record, so a Form workflow and an Application workflow
     * may share a name while duplicates within the same owner are rejected.
     */
    public function creating(Workflow $workflow): void
    {
        // The related_type/related_id columns only exist once the citext migration has
        // run, and that migration activates the flag. Gating on it keeps pre-migration
        // inserts (which run before the columns exist) from writing to absent columns.
        // TODO: Cleanup Task (workflow-name-citext) - remove the `WorkflowNameCitextFeature::active()` guard and always sync the owner columns.
        if (! WorkflowNameCitextFeature::active()) {
            return;
        }

        if ($workflow->getAttribute('related_type') !== null || $workflow->getAttribute('related_id') !== null) {
            return;
        }

        $trigger = $workflow->workflowTrigger;

        if ($trigger === null) {
            return;
        }

        $workflow->setAttribute('related_type', $trigger->related_type);
        $workflow->setAttribute('related_id', $trigger->related_id);
    }
}
