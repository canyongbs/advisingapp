<?php

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorCategory;
use Illuminate\Database\UniqueConstraintViolationException;

it('does not allow duplicate category names for the same advisor', function () {
    $qnaAdvisor = QnaAdvisor::factory()->create();

    QnaAdvisorCategory::factory()->state([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Admissions',
    ])->create();

    QnaAdvisorCategory::factory()->state([
        'qna_advisor_id' => $qnaAdvisor->getKey(),
        'name' => 'Admissions',
    ])->create();
})->throws(UniqueConstraintViolationException::class);

it('allow duplicate category names for the different advisor', function () {
    $qnaAdvisor = QnaAdvisor::factory()->has(QnaAdvisorCategory::factory()->state(['name' => 'Admission']), 'categories')->create();
    $qnaAdvisor2 = QnaAdvisor::factory()->has(QnaAdvisorCategory::factory()->state(['name' => 'Admission']), 'categories')->create();

    expect($qnaAdvisor->categories->first()->name)->toBe('Admission');
    expect($qnaAdvisor2->categories->first()->name)->toBe('Admission');
});
