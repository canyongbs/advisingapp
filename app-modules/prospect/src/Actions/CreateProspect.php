<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\DataTransferObjects\CreateProspectData;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
            $status = ProspectStatus::whereRaw('LOWER(name) = ?', [Str::lower($data->status)])->first();
            throw_if(
                ! $status,
                ValidationException::withMessages(['status' => 'Status does not exist.'])
            );

            $source = ProspectSource::whereRaw('LOWER(name) = ?', [Str::lower($data->source)])->first();
            throw_if(
                ! $source,
                ValidationException::withMessages(['source' => 'Source does not exist.'])
            );

            $prospect->fill($data->except('emailAddresses', 'status', 'source')->toArray());

            $prospect->status_id = $status->getKey();
            $prospect->source_id = $source->getKey();

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
