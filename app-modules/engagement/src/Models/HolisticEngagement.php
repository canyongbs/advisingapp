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

namespace AdvisingApp\Engagement\Models;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperHolisticEngagement
 */
class HolisticEngagement extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'holistic_engagements';

    protected $primaryKey = 'record_id';

    protected $casts = [
        'record_sortable_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function concern(): MorphTo
    {
        return $this->morphTo(
            name: 'concern',
            type: 'concern_type',
            id: 'concern_id',
        );
    }

    /**
     * @return MorphTo<covariant Student|Prospect|User, $this>
     */
    public function sentBy(): MorphTo
    {
        return $this->morphTo(
            name: 'sent_by',
            type: 'sent_by_type',
            id: 'sent_by_id',
        );
    }

    /**
     * @return MorphTo<covariant Student|Prospect, $this>
     */
    public function sentTo(): MorphTo
    {
        return $this->morphTo(
            name: 'sent_to',
            type: 'sent_to_type',
            id: 'sent_to_id',
        );
    }

    /**
     * @return MorphTo<contravariant Engagement|EngagementResponse, $this>
     */
    public function record(): MorphTo
    {
        return $this->morphTo(
            name: 'record',
            type: 'record_type',
            id: 'record_id',
            ownerKey: 'id',
        );
    }
}
