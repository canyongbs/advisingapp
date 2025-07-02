<?php

namespace AdvisingApp\CaseManagement\Rules;

use AdvisingApp\CaseManagement\Models\CaseType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CaseTypeAssignmentsIndividualUserMustBeAManager implements ValidationRule
{
    public function __construct(
        protected CaseType $caseType
    ) {}

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->caseType->managers()->whereRelation('users', 'users.id', $value)->doesntExist()) {
            $fail('The selected user must be in a team designated as managers of this Case Type.');
        }
    }
}
