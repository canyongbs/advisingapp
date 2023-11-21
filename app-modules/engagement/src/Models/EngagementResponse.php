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

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Assist\Prospect\Models\Prospect;
use Assist\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;
use Assist\Timeline\Timelines\EngagementResponseTimeline;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperEngagementResponse
 */
class EngagementResponse extends BaseModel implements Auditable, ProvidesATimeline
{
    use AuditableTrait;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'content',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): EngagementResponseTimeline
    {
        return new EngagementResponseTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->orderedEngagementResponses()->get();
    }

    public function sender(): MorphTo
    {
        return $this->morphTo(
            name: 'sender',
            type: 'sender_type',
            id: 'sender_id',
        );
    }

    public function scopeSentByStudent(Builder $query): void
    {
        $query->where('sender_type', resolve(Student::class)->getMorphClass());
    }

    public function scopeSentByProspect(Builder $query): void
    {
        $query->where('sender_type', resolve(Prospect::class)->getMorphClass());
    }
}
