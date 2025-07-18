<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEnrollments;

use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentsController
{
    public function __invoke(Request $request, Student $student): JsonResource
    {
        // This method should return a list of student enrollments.
        // The actual implementation will depend on your application's logic and data structure.
        // For now, we can return an empty resource or a placeholder response.

        return JsonResource::make([
            'message' => 'List of student enrollments will be implemented here.',
            'data' => [],
        ]);
    }
}
