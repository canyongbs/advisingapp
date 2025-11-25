<?php

namespace AdvisingApp\StudentDataModel\Policies;

use AdvisingApp\StudentDataModel\Models\Hold;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class HoldPolicy
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
            abilities: 'hold.view-any',
            denyResponse: 'You do not have permission to view holds.'
        );
    }

    public function view(Authenticatable $authenticatable, Hold $hold): Response
    {
        return $authenticatable->canOrElse(
            abilities: "hold.{$hold->getKey()}.view",
            denyResponse: 'You do not have permission to view this hold.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('Student data configuration is not enabled.');
        }

        return $authenticatable->canOrElse(
            abilities: 'hold.create',
            denyResponse: 'You do not have permission to create holds.'
        );
    }

    public function update(Authenticatable $authenticatable, Hold $hold): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('Student data configuration is not enabled.');
        }

        return $authenticatable->canOrElse(
            abilities: "hold.{$hold->getKey()}.update",
            denyResponse: 'You do not have permission to update this hold.'
        );
    }

    public function delete(Authenticatable $authenticatable, Hold $hold): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('Student data configuration is not enabled.');
        }

        return $authenticatable->canOrElse(
            abilities: "hold.{$hold->getKey()}.delete",
            denyResponse: 'You do not have permission to delete this hold.'
        );
    }

    public function restore(Authenticatable $authenticatable, Hold $hold): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('Student data configuration is not enabled.');
        }

        return $authenticatable->canOrElse(
            abilities: "hold.{$hold->getKey()}.restore",
            denyResponse: 'You do not have permission to restore this hold.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Hold $hold): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('Student data configuration is not enabled.');
        }

        return $authenticatable->canOrElse(
            abilities: "hold.{$hold->getKey()}.force-delete",
            denyResponse: 'You do not have permission to force delete this hold.'
        );
    }

    public function import(Authenticatable $authenticatable): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('Student data configuration is not enabled.');
        }

        return $authenticatable->canOrElse(
            abilities: 'hold.import',
            denyResponse: 'You do not have permission to import holds.'
        );
    }
}
