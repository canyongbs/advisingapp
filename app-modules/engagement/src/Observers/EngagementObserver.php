<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Observers;

use Illuminate\Database\Eloquent\Model;
use Assist\Engagement\Models\Engagement;
use Assist\Timeline\Events\TimelineableRecordCreated;
use Assist\Timeline\Events\TimelineableRecordDeleted;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class EngagementObserver
{
    public function creating(Engagement $engagement): void
    {
        if (is_null($engagement->user_id) && ! is_null(auth()->user())) {
            $engagement->user_id = auth()->user()->id;
        }
    }

    public function saving(Engagement $engagement): void
    {
        $engagement->deliver_at = $engagement->deliver_at ?? now();
    }

    public function created(Engagement $engagement): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $engagement);
        }

        /** @var Model $entity */
        $entity = $engagement->recipient;

        TimelineableRecordCreated::dispatch($entity, $engagement);
    }

    public function deleted(Engagement $engagement): void
    {
        /** @var Model $entity */
        $entity = $engagement->recipient;

        TimelineableRecordDeleted::dispatch($entity, $engagement);
    }
}
