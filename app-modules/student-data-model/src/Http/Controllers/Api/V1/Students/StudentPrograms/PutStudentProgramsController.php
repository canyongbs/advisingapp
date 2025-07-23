<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPrograms;

use AdvisingApp\StudentDataModel\Actions\PutStudentPrograms;
use AdvisingApp\StudentDataModel\DataTransferObjects\StudentProgramData;
use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentProgramResource;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class PutStudentProgramsController
{
    /**
     * @response StudentProgramResource
     */
    #[Group('Students')]
    public function __invoke(Request $request, PutStudentPrograms $program, Student $student): JsonResource
    {
        Gate::authorize('viewAny', Student::class);
        Gate::authorize('viewAny', Program::class);
        Gate::authorize('create', Program::class);

        foreach ($student->programs as $programToUpdate) {
            Gate::authorize('update', $programToUpdate);
        }

        foreach ($student->programs as $programToDelete) {
            Gate::authorize('delete', $programToDelete);
        }

        $data = $request->validate([
            'programs' => ['required', 'array'],
            'programs.*.acad_career' => ['sometimes', 'string', 'max:255'],
            'programs.*.division' => ['sometimes', 'string', 'max:255'],
            'programs.*.acad_plan' => ['required', 'array'],
            'programs.*.prog_status' => ['sometimes', 'string', 'max:255'],
            'programs.*.cum_gpa' => ['sometimes', 'decimal:0,2'],
            'programs.*.semester' => ['sometimes', 'string', 'max:255'],
            'programs.*.descr' => ['sometimes', 'string', 'max:255'],
            'programs.*.foi' => ['sometimes', 'string', 'max:255'],
            'programs.*.change_dt' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'programs.*.declare_dt' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
        ]);

        $programsData = StudentProgramData::collect($data['programs']);

        $program->execute($student, $programsData);

        return $student->refresh()->programs->toResourceCollection(StudentProgramResource::class);
    }
}
