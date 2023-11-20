<?php

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
