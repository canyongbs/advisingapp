<?php

namespace AdvisingApp\BasicNeeds\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\BasicNeeds\Models\BasicNeedsCategory;

class BasicNeedsCategoryPolicy
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
            abilities: 'basic_needs_category.view-any',
            denyResponse: 'You do not have permission to view basic needs categories.'
        );
    }

    public function view(Authenticatable $authenticatable, BasicNeedsCategory $basicNeedsCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_category.*.view', "basic_needs_category.{$basicNeedsCategory->id}.view"],
            denyResponse: 'You do not have permission to view this basic needs category.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'basic_needs_category.create',
            denyResponse: 'You do not have permission to create basic needs categories.'
        );
    }

    public function update(Authenticatable $authenticatable, BasicNeedsCategory $basicNeedsCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_category.*.update', "basic_needs_category.{$basicNeedsCategory->id}.update"],
            denyResponse: 'You do not have permission to update this basic needs category.'
        );
    }

    public function delete(Authenticatable $authenticatable, BasicNeedsCategory $basicNeedsCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_category.*.delete', "basic_needs_category.{$basicNeedsCategory->id}.delete"],
            denyResponse: 'You do not have permission to delete this basic needs category.'
        );
    }

    public function restore(Authenticatable $authenticatable, BasicNeedsCategory $basicNeedsCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_category.*.restore', "basic_needs_category.{$basicNeedsCategory->id}.restore"],
            denyResponse: 'You do not have permission to restore this basic needs category.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, BasicNeedsCategory $basicNeedsCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['basic_needs_category.*.force-delete', "basic_needs_category.{$basicNeedsCategory->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this basic needs category.'
        );
    }
}
