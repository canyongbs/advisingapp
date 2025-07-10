<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPrograms;

use AdvisingApp\StudentDataModel\Actions\UpdateStudentProgram;
use AdvisingApp\StudentDataModel\DataTransferObjects\StudentProgramData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentProgramResource;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class StudentProgramController
{
    /**
     * @response StudentProgramResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, UpdateStudentProgram $program, Student $student): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('create', Program::class);
        Gate::authorize('program.*.update', Program::class);
        Gate::authorize('program.*.delete', Program::class);
        
        $data = $request->validate([
            '*.acad_career' => ['sometimes', 'string', 'max:255'],
            '*.division' => ['sometimes', 'string', 'max:255'],
            '*.acad_plan' => ['required', 'string'],
            '*.prog_status' => ['sometimes', 'string', 'max:255'],
            '*.cum_gpa' => ['sometimes', 'decimal:0,2', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            '*.semester' => ['sometimes', 'string', 'max:255'],
            '*.descr' => ['sometimes', 'string', 'max:255'],
            '*.foi' => ['sometimes', 'string', 'max:255'],
            '*.change_dt' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            '*.declare_dt' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
        ]);

        $programData = $program->execute($student, StudentProgramData::collect($data));

        return $programData->toResourceCollection(StudentProgramResource::class);
    }
}
