<?php

namespace AdvisingApp\StudentDataModel\Policies;

use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class EnrollmentSemesterPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.view-any',
            denyResponse: 'You do not have permission to view enrollment semesters.'
        );
    }

    public function view(Authenticatable $authenticatable, EnrollmentSemester $enrollmentSemester): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.*.view',
            denyResponse: 'You do not have permission to view this enrollment semester.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.create',
            denyResponse: 'You do not have permission to create enrollment semesters.'
        );
    }

    public function update(Authenticatable $authenticatable, EnrollmentSemester $enrollmentSemester): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.update'],
            denyResponse: 'You do not have permission to update this enrollment semester.'
        );
    }

    public function delete(Authenticatable $authenticatable, EnrollmentSemester $enrollmentSemester): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.delete'],
            denyResponse: 'You do not have permission to delete this enrollment semester.'
        );
    }

    public function restore(Authenticatable $authenticatable, EnrollmentSemester $enrollmentSemester): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.restore'],
            denyResponse: 'You do not have permission to restore this enrollment semester.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, EnrollmentSemester $enrollmentSemester): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['settings.*.force-delete'],
            denyResponse: 'You do not have permission to permanently delete this enrollment semester.'
        );
    }
}
