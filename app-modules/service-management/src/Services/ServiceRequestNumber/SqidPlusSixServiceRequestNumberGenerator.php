<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\ServiceManagement\Services\ServiceRequestNumber;

use Sqids\Sqids;
use Assist\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;

class SqidPlusSixServiceRequestNumberGenerator implements ServiceRequestNumberGenerator
{
    public function generate(): string
    {
        $sqids = new Sqids(
            alphabet: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        );

        // Though in virtually all scenarios this generates an 8 character string, it is possible that it may generate one slightly longer
        // So we fill to 14 characters to ensure that we always have a 14 character string
        $encode = $sqids->encode([time()]);
        $length = strlen($encode);
        $remainingLength = 14 - $length;

        return $encode . $this->generateRandomString($remainingLength);
    }

    public function generateRandomString($length): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
