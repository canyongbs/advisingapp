<?php

use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisorCategory;
use Illuminate\Database\UniqueConstraintViolationException;

it('does not allow duplicate category names for the same advisor', function () {
    $qnAAdvisor = QnAAdvisor::factory()->create();

    QnAAdvisorCategory::factory()->state([
        'qn_a_advisor_id' => $qnAAdvisor->getKey(),
        'name' => 'Admissions',
    ])->create();

    QnAAdvisorCategory::factory()->state([
        'qn_a_advisor_id' => $qnAAdvisor->getKey(),
        'name' => 'Admissions',
    ])->create();
})->throws(UniqueConstraintViolationException::class);

it('allow duplicate category names for the different advisor', function () {
    $qnAAdvisor = QnAAdvisor::factory()->has(QnAAdvisorCategory::factory()->state(['name' => 'Admission']), 'categories')->create();
    $qnAAdvisor2 = QnAAdvisor::factory()->has(QnAAdvisorCategory::factory()->state(['name' => 'Admission']), 'categories')->create();

    expect($qnAAdvisor->categories->first()->name)->toBe('Admission');
    expect($qnAAdvisor2->categories->first()->name)->toBe('Admission');
});
