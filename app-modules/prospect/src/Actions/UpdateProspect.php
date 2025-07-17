<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\DataTransferObjects\UpdateProspectData;
use AdvisingApp\Prospect\Models\Prospect;

class UpdateProspect
{
    public function execute(Prospect $prospect, UpdateProspectData $data): Prospect
    {
        $prospect->fill($data->toArray());

        if ($data->status) {
            $prospect->status()->associate($data->status);
        }

        if ($data->source) {
            $prospect->source()->associate($data->source);
        }

        $prospect->save();

        return $prospect;
    }
}
