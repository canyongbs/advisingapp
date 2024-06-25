<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseloadManagement\Observers;

use App\Models\User;
use Laravel\Pennant\Feature;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\Segment\Models\SegmentSubject;
use AdvisingApp\CaseloadManagement\Models\Caseload;
use AdvisingApp\CaseloadManagement\Enums\CaseloadModel;

class CaseloadObserver
{
    public function creating(Caseload $caseload): void
    {
        $caseload->user()->associate($caseload->user ?? auth()->user());
    }

    public function created(Caseload $caseload): void
    {
        /** @var ?User $user */
        $user = auth()->user();

        if ($user) {
            Cache::tags([match ($caseload->model) {
                CaseloadModel::Prospect => "user-{$user->getKey()}-prospect-caseloads",
                CaseloadModel::Student => "user-{$user->getKey()}-student-caseloads",
            }])->flush();
        }

        if (! Feature::active('segment-as-caseload-replacement')) {
            $segment = new Segment();
            $segment->name = $caseload->name;
            $segment->description = $caseload->description;
            $segment->filters = $caseload->filters;
            $segment->model = $caseload->model;
            $segment->type = $caseload->type;
            $segment->user_id = $caseload->user_id;
            $segment->created_at = $caseload->created_at;
            $segment->updated_at = $caseload->updated_at;
            $segment->deleted_at = $caseload->deleted_at;

            if ($caseload->subjects()->withTrashed()->exists()) {
                foreach ($caseload->subjects()->withTrashed()->get() as $caseloadSubject) {
                    $segmentSubject = new SegmentSubject();
                    $segmentSubject->subject_id = $caseloadSubject->subject_id;
                    $segmentSubject->subject_type = $caseloadSubject->subject_type;
                    $segmentSubject->segment_id = $segment->id;
                    $segmentSubject->created_at = $caseloadSubject->created_at;
                    $segmentSubject->updated_at = $caseloadSubject->updated_at;
                    $segmentSubject->deleted_at = $caseloadSubject->deleted_at;
                }
            }
        }
    }

    public function deleted(Caseload $caseload): void
    {
        /** @var ?User $user */
        $user = auth()->user();

        if ($user) {
            Cache::tags([match ($caseload->model) {
                CaseloadModel::Prospect => "user-{$user->getKey()}-prospect-caseloads",
                CaseloadModel::Student => "user-{$user->getKey()}-student-caseloads",
            }])->flush();
        }
    }
}
