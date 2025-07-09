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

namespace AdvisingApp\CaseManagement\Actions;

use AdvisingApp\CaseManagement\Models\CaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notification;

class NotifyCaseUsers
{
    public function execute(CaseModel $case, Notification $notification, bool $shouldSendToManagers, bool $shouldSendToAuditors): void
    {
        if (! ($shouldSendToManagers || $shouldSendToAuditors)) {
            return;
        }

        $user = User::query()
            ->where(function (Builder $query) use ($case, $shouldSendToManagers, $shouldSendToAuditors) {
                if ($shouldSendToManagers) {
                    $query->whereHas(
                        'team',
                        fn (Builder $query) => $query->whereHas(
                            'managableCaseTypes',
                            fn (Builder $query) => $query->where('case_type_id', $case->priority->type->getKey())->whereHas(
                                'cases',
                                fn (Builder $query) => $query->whereKey($case),
                            ),
                        ),
                    );
                }

                if ($shouldSendToAuditors) {
                    $query->{$shouldSendToManagers ? 'orWhereHas' : 'whereHas'}(
                        'team',
                        fn (Builder $query) => $query->whereHas(
                            'auditableCaseTypes',
                            fn (Builder $query) => $query->where('case_type_id', $case->priority->type->getKey())->whereHas(
                                'cases',
                                fn (Builder $query) => $query->whereKey($case),
                            ),
                        )->whereDoesntHave(
                            'managableCaseTypes',
                            fn (Builder $query) => $query->where('case_type_id', $case->priority->type->getKey())->whereHas(
                                'cases',
                                fn (Builder $query) => $query->whereKey($case),
                            ),
                        ),
                    );
                }
            })
            ->get()
            ->each(fn (User $user) => $user->notify($notification));
    }
}
