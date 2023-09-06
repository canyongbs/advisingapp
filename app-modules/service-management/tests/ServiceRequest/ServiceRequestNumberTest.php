<?php

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

    $this->mock(ServiceRequestNumberGenerator::class, function (MockInterface $mock) use ($serviceRequest) {
        $mock->shouldReceive('generate')
            ->twice()
            ->andReturn($serviceRequest->service_request_number, '1234567891');
    });

    $newServiceRequest = ServiceRequest::factory()->create();

    expect($newServiceRequest->service_request_number)->toBe('1234567891');
});

test('ServiceRequestNumberExceededReRollsException will be thrown if the service_request_number is re-rolled more than allowed times', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $this->mock(ServiceRequestNumberGenerator::class, function (MockInterface $mock) use ($serviceRequest) {
        $mock->shouldReceive('generate')
            ->andReturn($serviceRequest->service_request_number);
    });

    ServiceRequest::factory()->create();
})->throws(ServiceRequestNumberExceededReRollsException::class);
