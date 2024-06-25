<?php

namespace AdvisingApp\BasicNeeds\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;

class BasicNeedsProgramPolicy
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
            abilities: 'basic_needs_program.view-any',
            denyResponse: 'You do not have permission to view basic needs programs.'
        );
    }

    public function view(Authenticatable $authenticatable, BasicNeedsProgram $basicNeedsProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_program.*.view', "basic_needs_program.{$basicNeedsProgram->id}.view"],
            denyResponse: 'You do not have permission to view this basic needs program.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'basic_needs_program.create',
            denyResponse: 'You do not have permission to create basic needs programs.'
        );
    }

    public function update(Authenticatable $authenticatable, BasicNeedsProgram $basicNeedsProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_program.*.update', "basic_needs_program.{$basicNeedsProgram->id}.update"],
            denyResponse: 'You do not have permission to update this basic needs program.'
        );
    }

    public function delete(Authenticatable $authenticatable, BasicNeedsProgram $basicNeedsProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_program.*.delete', "basic_needs_program.{$basicNeedsProgram->id}.delete"],
            denyResponse: 'You do not have permission to delete this basic needs program.'
        );
    }

    public function restore(Authenticatable $authenticatable, BasicNeedsProgram $basicNeedsProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_program.*.restore', "basic_needs_program.{$basicNeedsProgram->id}.restore"],
            denyResponse: 'You do not have permission to restore this basic needs program.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, BasicNeedsProgram $basicNeedsProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_program.*.force-delete', "basic_needs_program.{$basicNeedsProgram->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this basic needs program.'
        );
    }
}
