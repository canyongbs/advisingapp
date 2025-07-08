<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\DataTransferObjects\CreateProspectEmailAddressData;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use Illuminate\Support\Facades\DB;

class CreateProspectEmailAddress
{
    public function execute(Prospect $prospect, CreateProspectEmailAddressData $data): ProspectEmailAddress
    {
        return DB::transaction(function () use ($data, $prospect) {
            $emailAddress = new ProspectEmailAddress();
            $emailAddress->prospect()->associate($prospect);
            $emailAddress->fill($data->toArray());
            $emailAddress->save();

            if (! $prospect->primaryEmailAddress()->exists()) {
                $prospect->primaryEmailAddress()->associate($prospect->emailAddresses->first());
                $prospect->save();
            }

            return $emailAddress;
        });
    }
}
