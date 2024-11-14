<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Mockery\MockInterface;
use AdvisingApp\CaseManagement\Models\ServiceRequest;
use AdvisingApp\CaseManagement\Exceptions\ServiceRequestNumberUpdateAttemptException;
use AdvisingApp\CaseManagement\Exceptions\ServiceRequestNumberExceededReRollsException;
use AdvisingApp\CaseManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;

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
