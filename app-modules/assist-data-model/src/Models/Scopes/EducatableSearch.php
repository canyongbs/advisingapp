<?php

namespace Assist\AssistDataModel\Models\Scopes;

use Illuminate\Support\Facades\DB;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;

class EducatableSearch
{
    public function __construct(
        protected string $relationship,
        protected string $search
    ) {}

    public function __invoke(Builder $query): void
    {
        $search = strtolower($this->search);

        $query->whereHasMorph(
            $this->relationship,
            [Student::class, Prospect::class],
            function (Builder $query, string $type) use ($search) {
                $column = app($type)::displayNameKey();

                $query->where(
                    DB::raw("LOWER({$column})"),
                    'like',
                    "%{$search}%"
                );
            }
        );
    }
}
