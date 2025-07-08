<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\DataTransferObjects\UpdateProspectEmailAddressData;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;

class UpdateProspectEmailAddress
{
    public function execute(ProspectEmailAddress $prospectEmailAddress, UpdateProspectEmailAddressData $data): ProspectEmailAddress
    {
        $prospectEmailAddress->fill($data->toArray());
        $prospectEmailAddress->save();

        return $prospectEmailAddress;
    }
}
