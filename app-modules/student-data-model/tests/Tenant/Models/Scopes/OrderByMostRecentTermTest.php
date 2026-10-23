<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\StudentDataModel\Models\Scopes\OrderByMostRecentTerm;
use AdvisingApp\StudentDataModel\Models\Term;

it('orders terms by start date, then by `order`, then by name', function () {
    $termWithoutOrder = Term::factory()->withoutSisTermId()->create(['name' => 'Winter 2012']);
    $olderTerm = Term::factory()->create(['start_date' => '2020-09-01']);
    $secondNamedTerm = Term::factory()->withoutSisTermId()->create(['name' => 'Summer 2012', 'order' => 1]);
    $higherOrderedTerm = Term::factory()->withoutSisTermId()->create(['name' => 'Fall 2012', 'order' => 2]);
    $recentTerm = Term::factory()->create(['start_date' => '2021-09-01']);
    $firstNamedTerm = Term::factory()->withoutSisTermId()->create(['name' => 'Spring 2012', 'order' => 1]);

    expect(Term::query()->tap(new OrderByMostRecentTerm())->pluck('id')->all())->toBe([
        $recentTerm->getKey(),
        $olderTerm->getKey(),
        $higherOrderedTerm->getKey(),
        $firstNamedTerm->getKey(),
        $secondNamedTerm->getKey(),
        $termWithoutOrder->getKey(),
    ]);
});
