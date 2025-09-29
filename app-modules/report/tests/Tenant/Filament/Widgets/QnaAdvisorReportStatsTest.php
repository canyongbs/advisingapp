<?php

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\QnaAdvisorReportStats;
use AdvisingApp\StudentDataModel\Models\Student;

it('returns correct total QnaAdvisor stats of QnaAdvisors, students, prospects and unauthenticated within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $qnaAdvisorCountStart = random_int(1, 5);
    $qnaAdvisorCountEnd = random_int(1, 5);
    $studentsCount = random_int(1, 5);
    $prospectsCount = random_int(1, 5);
    $unauthenticatedCount = random_int(1, 5);

    QnaAdvisor::factory()->count($qnaAdvisorCountStart)->state([
        'created_at' => $startDate,
    ])->create();

    QnaAdvisor::factory()->count($qnaAdvisorCountEnd)->state([
        'created_at' => $endDate,
    ])->create();

    QnaAdvisorThread::factory()
        ->count($studentsCount)
        ->for(QnaAdvisor::factory(), 'advisor')
        ->for(Student::factory(), 'author')
        ->state([
            'created_at' => $startDate,
        ])
        ->create();

    QnaAdvisorThread::factory()
        ->count($prospectsCount)
        ->for(QnaAdvisor::factory(), 'advisor')
        ->for(Prospect::factory(), 'author')
        ->state([
            'created_at' => $startDate,
        ])
        ->create();

    QnaAdvisorThread::factory()
        ->count($unauthenticatedCount)
        ->for(QnaAdvisor::factory(), 'advisor')
        ->state([
            'created_at' => $startDate,
        ])
        ->create();

    $widget = new QnaAdvisorReportStats();
    $widget->cacheTag = 'qna-advisor-report-cache';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($qnaAdvisorCountStart + $qnaAdvisorCountEnd)
        ->and($stats[1]->getValue())->toEqual($studentsCount)
        ->and($stats[2]->getValue())->toEqual($prospectsCount)
        ->and($stats[3]->getValue())->toEqual($unauthenticatedCount);
});
