<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\DataTransferObjects\CreateProspectData;
use AdvisingApp\Prospect\DataTransferObjects\CreateProspectEmailAddressData;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Support\Facades\DB;

class CreateProspect
{
    public function __construct(
        protected CreateProspectEmailAddressData $createProspectEmailAddress,
    ) {}

    public function execute(CreateProspectData $data): Prospect
    {
        return DB::transaction(function () use ($data) {
            $prospect = new Prospect();
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
