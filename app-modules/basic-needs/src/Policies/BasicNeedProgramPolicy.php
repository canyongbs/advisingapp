<?php

namespace AdvisingApp\BasicNeeds\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\BasicNeeds\Models\BasicNeedProgram;

class BasicNeedProgramPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasLicense(Student::getLicenseType())) {
            return Response::deny('You are not licensed for the Retention CRM.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'basic_need_program.view-any',
            denyResponse: 'You do not have permission to view basic need program.'
        );
    }

    public function view(Authenticatable $authenticatable, BasicNeedProgram $basicNeedProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_program.*.view', "basic_need_program.{$basicNeedProgram->id}.view"],
            denyResponse: 'You do not have permission to view this basic need program.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'basic_need_program.create',
            denyResponse: 'You do not have permission to create basic need program.'
        );
    }

    public function update(Authenticatable $authenticatable, BasicNeedProgram $basicNeedProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_program.*.update', "basic_need_program.{$basicNeedProgram->id}.update"],
            denyResponse: 'You do not have permission to update this basic need program.'
        );
    }

    public function delete(Authenticatable $authenticatable, BasicNeedProgram $basicNeedProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_program.*.delete', "basic_need_program.{$basicNeedProgram->id}.delete"],
            denyResponse: 'You do not have permission to delete this basic need program.'
        );
    }

    public function restore(Authenticatable $authenticatable, BasicNeedProgram $basicNeedProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_program.*.restore', "basic_need_program.{$basicNeedProgram->id}.restore"],
            denyResponse: 'You do not have permission to restore this basic need program.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, BasicNeedProgram $basicNeedProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_program.*.force-delete', "basic_need_program.{$basicNeedProgram->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this basic need program.'
        );
    }
}
