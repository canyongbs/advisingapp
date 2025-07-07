<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    ...existing copyright...

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Actions;

use AdvisingApp\StudentDataModel\DataTransferObjects\UpdateStudentEmailAddressData;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;

class UpdateStudentEmailAddress
{
    public function execute(StudentEmailAddress $studentEmailAddress, UpdateStudentEmailAddressData $data): StudentEmailAddress
    {
        $studentEmailAddress->fill($data->toArray());
        $studentEmailAddress->save();

        return $studentEmailAddress;
    }
}
