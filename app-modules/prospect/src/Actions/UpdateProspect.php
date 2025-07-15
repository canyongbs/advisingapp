<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\DataTransferObjects\UpdateProspectData;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateProspect
{
    public function execute(Prospect $prospect, UpdateProspectData $data): Prospect
    {
        $prospect->fill($data->toArray());

        if ($data->status) {
            $status = ProspectStatus::whereRaw('LOWER(name) = ?', [Str::lower($data->status)])->first();
            throw_if(
                ! $status,
                ValidationException::withMessages(['status' => 'Status does not exist.'])
            );
            $prospect->status_id = $status->getKey();
        }

        if ($data->source) {
            $source = ProspectSource::whereRaw('LOWER(name) = ?', [Str::lower($data->source)])->first();
            throw_if(
                ! $source,
                ValidationException::withMessages(['source' => 'Source does not exist.'])
            );
            $prospect->source_id = $source->getKey();
        }

        $prospect->save();

        return $prospect;
    }
}
