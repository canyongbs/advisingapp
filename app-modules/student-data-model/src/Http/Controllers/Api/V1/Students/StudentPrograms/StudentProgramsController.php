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

class StudentProgramsController
{
    /**
     * @response StudentProgramResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, UpdateStudentProgram $program, Student $student): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('viewAny', Program::class);
        Gate::authorize('create', Program::class);

        $data = $request->validate([
            'program' => ['required','array'],
            'program.*.acad_career' => ['sometimes', 'string', 'max:255'],
            'program.*.division' => ['sometimes', 'string', 'max:255'],
            'program.*.acad_plan' => ['required', 'string','max:255'],
            'program.*.prog_status' => ['sometimes', 'string', 'max:255'],
            'program.*.cum_gpa' => ['sometimes', 'decimal:0,2'],
            'program.*.semester' => ['sometimes', 'string', 'max:255'],
            'program.*.descr' => ['sometimes', 'string', 'max:255'],
            'program.*.foi' => ['sometimes', 'string', 'max:255'],
            'program.*.change_dt' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'program.*.declare_dt' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
        ]);

        $data = $data['program'];

        $programData = $program->execute($student, StudentProgramData::collect($data));

        return $programData->toResourceCollection(StudentProgramResource::class);
    }
}
