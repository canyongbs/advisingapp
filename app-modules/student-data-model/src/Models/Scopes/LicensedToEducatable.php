<?php

namespace AdvisingApp\StudentDataModel\Models\Scopes;

use Exception;
use App\Models\Authenticatable;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LicensedToEducatable
{
    public function __construct(
        protected string $relationship,
    ) {}

    public function __invoke(Builder $query): void
    {
        if (! auth()->check()) {
            return;
        }

        /** @var Authenticatable $user */
        $user = auth()->user();

        $model = $query->getModel();

        if (
            (! method_exists($model, $this->relationship)) ||
            (! ($model->{$this->relationship}() instanceof MorphTo))
        ) {
            throw new Exception('The [' . static::class . "] model does not have a [{$this->relationship}] [" . MorphTo::class . '] relationship where educatables can be assigned.');
        }

        $typeColumn = $model->{$this->relationship}()->getMorphType();

        $query
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
