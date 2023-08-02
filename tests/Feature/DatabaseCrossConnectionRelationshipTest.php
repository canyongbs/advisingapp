<?php

use Assist\Case\Models\CaseItem;
use Assist\AssistDataModel\Models\Student;

test('relationships work cross connections', function () {
    $student = Student::factory()
        ->has(
            CaseItem::factory()
                ->count(3),
            'cases'
        )
        ->create();

    expect($student->cases)->toHaveCount(3);

    Student::factory()->create();

    expect(Student::all())->toHaveCount(2);

    $whereHas = Student::whereHas('cases', function ($query) {
        $query->whereNotNull('res_details');
    })->get();

    expect($whereHas)->toHaveCount(1);
});
