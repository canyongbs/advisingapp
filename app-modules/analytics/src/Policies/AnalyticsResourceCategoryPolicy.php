<?php

namespace AdvisingApp\Analytics\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;

class AnalyticsResourceCategoryPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'analytics_resource_category.view-any',
            denyResponse: 'You do not have permission to view analytics resource categories.'
        );
    }

    public function view(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.view', "analytics_resource_category.{$analyticsResourceCategory->id}.view"],
            denyResponse: 'You do not have permission to view this analytics resource category.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'analytics_resource_category.create',
            denyResponse: 'You do not have permission to create analytics resource categories.'
        );
    }

    public function update(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.update', "analytics_resource_category.{$analyticsResourceCategory->id}.update"],
            denyResponse: 'You do not have permission to update this analytics resource category.'
        );
    }

    public function delete(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.delete', "analytics_resource_category.{$analyticsResourceCategory->id}.delete"],
            denyResponse: 'You do not have permission to delete this analytics resource category.'
        );
    }

    public function restore(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.restore', "analytics_resource_category.{$analyticsResourceCategory->id}.restore"],
            denyResponse: 'You do not have permission to restore this analytics resource category.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.force-delete', "analytics_resource_category.{$analyticsResourceCategory->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this analytics resource category.'
        );
    }
}
