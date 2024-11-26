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
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Exceptions\CaseNumberUpdateAttemptException;
use AdvisingApp\CaseManagement\Exceptions\CaseNumberExceededReRollsException;
use AdvisingApp\CaseManagement\Cases\CaseNumber\Contracts\CaseNumberGenerator;

test('An Exception is thrown if it is attempted to change the case_number', function () {
    $case = CaseModel::factory()->create();

    $case->case_number = '1234567890';

    $case->save();
})->throws(CaseNumberUpdateAttemptException::class);

test('A save is attempted again and the case_number re-rolled if a UniqueConstraintViolationException is thrown', function () {
    $case = CaseModel::factory()->create();

    app()->instance(CaseNumberGenerator::class, mock(CaseNumberGenerator::class, function (MockInterface $mock) use ($case) {
        $mock->shouldReceive('generate')
            ->twice()
            ->andReturn($case->case_number, '1234567891');
    }));

    $newCase = CaseModel::factory()->create();

    expect($newCase->case_number)->toBe('1234567891');
});

test('CaseNumberExceededReRollsException will be thrown if the case_number is re-rolled more than allowed times', function () {
    $case = CaseModel::factory()->create();

    app()->instance(CaseNumberGenerator::class, mock(CaseNumberGenerator::class, function (MockInterface $mock) use ($case) {
        $mock->shouldReceive('generate')
            ->andReturn($case->case_number);
    }));

    CaseModel::factory()->create();
})->throws(CaseNumberExceededReRollsException::class);
