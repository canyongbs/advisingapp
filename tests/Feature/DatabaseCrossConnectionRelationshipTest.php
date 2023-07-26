<?php

namespace Tests\Feature;

use Tests\TestCase;
use Assist\Case\Models\CaseItem;
use Assist\AssistDataModel\Models\Student;

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

        Student::factory()->create();

        $this->assertCount(2, Student::all());

        $whereHas = Student::whereHas('cases', function ($query) {
            $query->whereNotNull('res_details');
        })->get();

        $this->assertCount(1, $whereHas);
    }
}
