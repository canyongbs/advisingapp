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

use Mockery\MockInterface;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Exceptions\ServiceRequestNumberUpdateAttemptException;
use Assist\ServiceManagement\Exceptions\ServiceRequestNumberExceededReRollsException;
use Assist\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;

test('An Exception is thrown if it is attempted to change the service_request_number', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $serviceRequest->service_request_number = '1234567890';

    $serviceRequest->save();
})->throws(ServiceRequestNumberUpdateAttemptException::class);

test('A save is attempted again and the service_request_number re-rolled if a UniqueConstraintViolationException is thrown', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    app()->instance(ServiceRequestNumberGenerator::class, mock(ServiceRequestNumberGenerator::class, function (MockInterface $mock) use ($serviceRequest) {
        $mock->shouldReceive('generate')
            ->twice()
            ->andReturn($serviceRequest->service_request_number, '1234567891');
    }));

    $newServiceRequest = ServiceRequest::factory()->create();

    expect($newServiceRequest->service_request_number)->toBe('1234567891');
});

test('ServiceRequestNumberExceededReRollsException will be thrown if the service_request_number is re-rolled more than allowed times', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    app()->instance(ServiceRequestNumberGenerator::class, mock(ServiceRequestNumberGenerator::class, function (MockInterface $mock) use ($serviceRequest) {
        $mock->shouldReceive('generate')
            ->andReturn($serviceRequest->service_request_number);
    }));

    ServiceRequest::factory()->create();
})->throws(ServiceRequestNumberExceededReRollsException::class);
