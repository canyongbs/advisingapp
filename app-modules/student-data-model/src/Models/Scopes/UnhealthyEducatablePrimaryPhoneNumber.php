<?php

namespace AdvisingApp\StudentDataModel\Models\Scopes;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UnhealthyEducatablePrimaryPhoneNumber
{
    /**
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    public function __invoke(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereDoesntHave('primaryPhoneNumber')
                ->orWhereHas('primaryPhoneNumber', function (Builder $query) {
                    $query->where('can_receive_sms', false)
                        ->orWhereHas('smsOptOut');
                })
                ->orWhere('sms_opt_out', true);
        });
    }
}
