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
    }
}
