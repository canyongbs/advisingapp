<?php

namespace AdvisingApp\StudentDataModel\Models\Scopes;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UnhealthyEducatablePrimaryEmailAddress
{
    /**
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    public function __invoke(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereDoesntHave('primaryEmailAddress')
                ->orWhereHas('primaryEmailAddress', function (Builder $query) {
                    $query->whereHas('bounced')
                        ->orWhereHas('optedOut', function (Builder $query) {
                            $query->where('status', EmailAddressOptInOptOutStatus::OptedOut);
                        });
                })
                ->orWhere('email_bounce', true);
        });
    }
}
