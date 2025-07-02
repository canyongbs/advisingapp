<?php

namespace AdvisingApp\CaseManagement\Services\CaseType;

use AdvisingApp\CaseManagement\Models\CaseModel;

interface CaseTypeAssigner
{
    public function execute(CaseModel $case): void;
}
