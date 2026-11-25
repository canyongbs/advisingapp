<?php

namespace AdvisingApp\Organization\Actions;

use AdvisingApp\Prospect\Models\Prospect;

class MatchProspectToOrganization
{
    public function __invoke(Prospect $prospect): void {}
}
