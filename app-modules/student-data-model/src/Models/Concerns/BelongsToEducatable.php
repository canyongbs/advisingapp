<?php

namespace AdvisingApp\StudentDataModel\Models\Concerns;

use Exception;
use App\Models\Authenticatable;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait BelongsToEducatable
{
    public function scopeLicensedToEducatable(Builder $query, string $relationship): Builder
    {
        if (! auth()->check()) {
            return $query;
        }

        /** @var Authenticatable $user */
        $user = auth()->user();

        if (
            (! method_exists($this, $relationship)) ||
            (! ($this->{$relationship}() instanceof MorphTo))
        ) {
            throw new Exception('The [' . static::class . "] model does not have a [{$relationship}] [" . MorphTo::class . '] relationship where educatables can be assigned.');
        }

        $typeColumn = $this->{$relationship}()->getMorphType();

        return $query
            ->when(
                ! $user->hasLicense(Student::getLicenseType()),
                fn (Builder $query) => $query->where($typeColumn, '!=', app(Student::class)->getMorphClass()),
            )
            ->when(
                ! $user->hasLicense(Prospect::getLicenseType()),
                fn (Builder $query) => $query->where($typeColumn, '!=', app(Prospect::class)->getMorphClass()),
            );
    }
}
