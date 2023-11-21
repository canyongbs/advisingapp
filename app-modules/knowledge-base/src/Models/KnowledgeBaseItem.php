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

namespace Assist\KnowledgeBase\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Assist\Division\Models\Division;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperKnowledgeBaseItem
 */
class KnowledgeBaseItem extends BaseModel implements Auditable, HasMedia
{
    use AuditableTrait;
    use HasUuids;
    use InteractsWithMedia;

    protected $casts = [
        'public' => 'boolean',
    ];

    protected $fillable = [
        'question',
        'quality_id',
        'status_id',
        'public',
        'category_id',
        'solution',
        'notes',
    ];

    public function quality(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseQuality::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseStatus::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseCategory::class);
    }

    public function division(): BelongsToMany
    {
        return $this->belongsToMany(Division::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('solution');
        $this->addMediaCollection('notes');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
