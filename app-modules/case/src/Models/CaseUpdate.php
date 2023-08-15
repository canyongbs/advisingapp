<?php

namespace Assist\Case\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Assist\Case\Enums\CaseUpdateDirection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Assist\Case\Database\Factories\CaseUpdateFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Case\Models\CaseUpdate
 *
 * @property int $id
 * @property int|null $case_id
 * @property string $update
 * @property bool $internal
 * @property CaseUpdateDirection $direction
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read CaseItem|null $case
 *
 * @method static CaseUpdateFactory factory($count = null, $state = [])
 * @method static Builder|CaseUpdate newModelQuery()
 * @method static Builder|CaseUpdate newQuery()
 * @method static Builder|CaseUpdate onlyTrashed()
 * @method static Builder|CaseUpdate query()
 * @method static Builder|CaseUpdate whereCaseId($value)
 * @method static Builder|CaseUpdate whereCreatedAt($value)
 * @method static Builder|CaseUpdate whereDeletedAt($value)
 * @method static Builder|CaseUpdate whereDirection($value)
 * @method static Builder|CaseUpdate whereId($value)
 * @method static Builder|CaseUpdate whereInternal($value)
 * @method static Builder|CaseUpdate whereUpdate($value)
 * @method static Builder|CaseUpdate whereUpdatedAt($value)
 * @method static Builder|CaseUpdate withTrashed()
 * @method static Builder|CaseUpdate withoutTrashed()
 *
 * @mixin Eloquent
 */
class CaseUpdate extends BaseModel
{
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        'case_id',
        'update',
        'internal',
        'direction',
    ];

    protected $casts = [
        'internal' => 'boolean',
        'direction' => CaseUpdateDirection::class,
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseItem::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
