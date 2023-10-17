<?php

namespace Assist\CaseloadManagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\CaseloadManagement\Actions\TranslateCaseloadFilters;

/**
 * @mixin IdeHelperCaseload
 */
class Caseload extends BaseModel
{
    protected $fillable = [
        'query',
        'filters',
        'name',
        'model',
        'type',
    ];

    protected $casts = [
        'filters' => 'array',
        'model' => CaseloadModel::class,
        'type' => CaseloadType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(CaseloadSubject::class);
    }

    public function retrieveRecords(): Collection
    {
        ray('retrieveRecords()');

        if (count($this->subjects) > 0) {
            return $this->subjects;
        }

        /** @var Builder $modelQueryBuilder */
        $modelQueryBuilder = $this->model->query();

        ray('modelQueryBuilder', $modelQueryBuilder);

        $records = $modelQueryBuilder
            ->whereKey(
                app(TranslateCaseloadFilters::class)
                    ->handle($this)
                    // TODO Make this flexible enough to handle the various cases
                    ->pluck('sisid'),
            )
            ->get();

        ray('records', $records);
    }
}
