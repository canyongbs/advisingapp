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

namespace AdvisingApp\Segment\Models;

use App\Models\User;
use App\Models\BaseModel;
use App\Models\Authenticatable;
use Illuminate\Support\Collection;
use AdvisingApp\Campaign\Models\Campaign;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Enums\SegmentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Segment\Actions\TranslateSegmentFilters;

/**
 * @mixin IdeHelperSegment
 */
class Segment extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'query',
        'filters',
        'name',
        'description',
        'model',
        'type',
    ];

    protected $casts = [
        'filters' => 'array',
        'model' => SegmentModel::class,
        'type' => SegmentType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(SegmentSubject::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function scopeModel(Builder $query, SegmentModel $model): void
    {
        $query->where('model', $model);
    }

    public function retrieveRecords(): Collection
    {
        /** @var Builder $modelQueryBuilder */
        $modelQueryBuilder = $this->model->query();

        $class = $this->model->class();

        if (count($this->subjects) > 0) {
            return $this->subjects->map(function (SegmentSubject $subject) {
                return $subject->subject;
            });
        }

        return $modelQueryBuilder
            ->whereKey(
                resolve(TranslateSegmentFilters::class)
                    ->handle($this)
                    ->pluck(resolve($class)->getKeyName()),
            )
            ->get();
    }

    public function retrieveEducatablesRecords(): Builder
    {
        /** @var Builder $modelQueryBuilder */
        $modelQueryBuilder = $this->model->query();
        
        $class = $this->model->class();

        if (count($this->subjects) > 0) {
            return $modelQueryBuilder->whereIn('id', function ($query) use ($class) {
                $query->select('subject_id')
                    ->from((new SegmentSubject)->getTable())
                    ->where('subject_type', resolve($class)->getMorphClass())
                    ->where('segment_id', $this->getKey());
            });
        }

        return $modelQueryBuilder
            ->whereKey(
                resolve(TranslateSegmentFilters::class)
                    ->handle($this)
                    ->pluck(resolve($class)->getKeyName()),
            );
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            if (! auth()->check()) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->user();

            foreach (SegmentModel::cases() as $model) {
                if (! $user->hasLicense($model->class()::getLicenseType())) {
                    $builder->where('model', '!=', $model);
                }
            }
        });
    }
}
