<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\DataTransferObjects\UpdateProspectData;
use AdvisingApp\Prospect\Models\Prospect;

class UpdateProspect
{
    public function execute(Prospect $prospect, UpdateProspectData $data): Prospect
    {
        $prospect->fill($data->toArray());
        $prospect->save();

        return $prospect;
    }
}
