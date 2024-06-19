<?php

namespace AdvisingApp\BasicNeeds\Policies;

use AdvisingApp\BasicNeeds\Models\BasicNeedCategory;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class BasicNeedCategoryPolicy
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
            abilities: 'basic_need_category.view-any',
            denyResponse: 'You do not have permission to view basic need category.'
        );
    }

    public function view(Authenticatable $authenticatable, BasicNeedCategory $basicNeedCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_category.*.view', "basic_need_category.{$basicNeedCategory->id}.view"],
            denyResponse: 'You do not have permission to view this basic need category.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'basic_need_category.create',
            denyResponse: 'You do not have permission to create basic need category.'
        );
    }

    public function update(Authenticatable $authenticatable, BasicNeedCategory $basicNeedCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_category.*.update', "basic_need_category.{$basicNeedCategory->id}.update"],
            denyResponse: 'You do not have permission to update this basic need category.'
        );
    }

    public function delete(Authenticatable $authenticatable, BasicNeedCategory $basicNeedCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_category.*.delete', "basic_need_category.{$basicNeedCategory->id}.delete"],
            denyResponse: 'You do not have permission to delete this basic need category.'
        );
    }

    public function restore(Authenticatable $authenticatable, BasicNeedCategory $basicNeedCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_category.*.restore', "basic_need_category.{$basicNeedCategory->id}.restore"],
            denyResponse: 'You do not have permission to restore this basic need category.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, BasicNeedCategory $basicNeedCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_need_category.*.force-delete', "basic_need_category.{$basicNeedCategory->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this basic need category.'
        );
    }
}
