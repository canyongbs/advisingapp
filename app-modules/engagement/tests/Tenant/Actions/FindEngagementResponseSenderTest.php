<?php

use AdvisingApp\Engagement\Actions\Contracts\EngagementResponseSenderFinder;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;

it('can match to a Student', function () {
    /** @var Student $student */
    $student = Student::factory()->create();
    $phoneNumber = $student->phoneNumbers->first()->number;

    $sender = app(EngagementResponseSenderFinder::class)->find($phoneNumber);

    expect($student->is($sender))->toBeTrue();
});

it('can match to a Prospect', function () {
    /** @var Prospect $prospect */
    $prospect = Prospect::factory()->create();
    $phoneNumber = $prospect->phoneNumbers->first()->number;

    $sender = app(EngagementResponseSenderFinder::class)->find($phoneNumber);

    expect($prospect->is($sender))->toBeTrue();
});

it('returns null when no match is found', function () {
    /** @var Student $student */
    $student = Student::factory()->create();

    /** @var Prospect $prospect */
    $prospect = Prospect::factory()->create();

    $phoneNumber = '1234567890';

    $sender = app(EngagementResponseSenderFinder::class)->find($phoneNumber);

    expect($sender)->toBeNull();
});
