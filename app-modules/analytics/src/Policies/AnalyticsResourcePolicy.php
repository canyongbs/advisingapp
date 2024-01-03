<?php

namespace AdvisingApp\Analytics\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\Analytics\Models\AnalyticsResource;

class AnalyticsResourcePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'analytics_resource.view-any',
            denyResponse: 'You do not have permission to view analytics resources.'
        );
    }

    public function view(Authenticatable $authenticatable, AnalyticsResource $analyticsResource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource.*.view', "analytics_resource.{$analyticsResource->id}.view"],
            denyResponse: 'You do not have permission to view this analytics resource.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'analytics_resource.create',
            denyResponse: 'You do not have permission to create analytics resources.'
        );
    }

    public function update(Authenticatable $authenticatable, AnalyticsResource $analyticsResource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource.*.update', "analytics_resource.{$analyticsResource->id}.update"],
            denyResponse: 'You do not have permission to update this analytics resource.'
        );
    }

    public function delete(Authenticatable $authenticatable, AnalyticsResource $analyticsResource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource.*.delete', "analytics_resource.{$analyticsResource->id}.delete"],
            denyResponse: 'You do not have permission to delete this analytics resource.'
        );
    }

    public function restore(Authenticatable $authenticatable, AnalyticsResource $analyticsResource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource.*.restore', "analytics_resource.{$analyticsResource->id}.restore"],
            denyResponse: 'You do not have permission to restore this analytics resource.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, AnalyticsResource $analyticsResource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource.*.force-delete', "analytics_resource.{$analyticsResource->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this analytics resource.'
        );
    }
}
