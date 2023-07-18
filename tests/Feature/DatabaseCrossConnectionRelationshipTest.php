<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\CaseItem;

class DatabaseCrossConnectionRelationshipTest extends TestCase
{
    /** @test */
    public function relationships_work_cross_connections(): void
    {
        $student = Student::factory()
            ->has(
                CaseItem::factory()
                    ->count(3),
                'cases'
            )
            ->create();

        $this->assertCount(3, $student->cases);

        $whereHasEquivalent = Student::where(function ($query) {
            $cases = CaseItem::whereNotNull('res_details')->get();

            $query->whereIn('student_id', $cases->pluck('respondent_id'));
        })->get();

        $this->assertCount(1, $whereHasEquivalent);
    }
}
