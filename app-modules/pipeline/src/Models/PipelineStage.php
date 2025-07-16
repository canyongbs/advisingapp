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

namespace AdvisingApp\Pipeline\Models;

use AdvisingApp\Pipeline\Database\Factories\PipelineStageFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read Collection<int, EducatablePipelineStage> $educatables
 * @property-read int|null $educatables_count
 * @property-read Pipeline|null $pipeline
 *
 * @method static \AdvisingApp\Pipeline\Database\Factories\PipelineStageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PipelineStage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PipelineStage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PipelineStage query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperPipelineStage
 */
class PipelineStage extends Model
{
    /** @use HasFactory<PipelineStageFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'name',
        'pipeline_id',
        'order',
    ];

    /**
     * @return BelongsTo<Pipeline, $this>
     */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    /**
     * @return HasMany<EducatablePipelineStage, $this>
     */
    public function educatables(): HasMany
    {
        return $this->hasMany(EducatablePipelineStage::class, 'pipeline_stage_id');
    }
}
