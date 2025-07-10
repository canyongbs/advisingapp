<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\DataTransferObjects\CreateProspectData;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateProspect
{
    public function __construct(
        protected CreateProspectEmailAddress $createProspectEmailAddress,
    ) {}

    public function execute(CreateProspectData $data): Prospect
    {
        return DB::transaction(function () use ($data) {
            $prospect = new Prospect();
            $status = ProspectStatus::where('name', $data->status_id)->first();
            throw_if(
                ! $status,
                ValidationException::withMessages(['status_id' => 'Status does not exist.'])
            );
            $data->status_id = $status->getKey();

            $source = ProspectSource::where('name', $data->source_id)->first();
            throw_if(
                ! $source,
                ValidationException::withMessages(['source_id' => 'Source does not exist.'])
            );
            $data->source_id = $source->getKey();

            $prospect->fill($data->except('emailAddresses')->toArray());
            $prospect->save();

            if (filled($data->emailAddresses)) {
                foreach ($data->emailAddresses as $emailData) {
                    $this->createProspectEmailAddress->execute($prospect, $emailData);
                }
            }

            return $prospect;
        });
    }
}
