<?php

namespace Assist\Case\Models;

use Eloquent;
use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\RecordStudentItem;
use Illuminate\Database\Eloquent\Builder;
use Assist\Case\Enums\CaseUpdateDirection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Assist\Case\Models\CaseUpdate
 *
 * @property int $id
 * @property string $update
 * @property string $internal
 * @property string $direction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $student_id
 * @property int|null $case_id
 * @property-read \Assist\Case\Models\CaseItem|null $case
 * @property-read mixed $direction_label
 * @property-read mixed $internal_label
 * @property-read RecordStudentItem|null $student
 *
 * @method static Builder|CaseUpdate advancedFilter($data)
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
 * @method static Builder|CaseUpdate whereStudentId($value)
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

    // TODO: Should this exist as a through relation?
    //public function student(): BelongsTo
    //{
    //    return $this->belongsTo(RecordStudentItem::class);
    //}

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseItem::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
