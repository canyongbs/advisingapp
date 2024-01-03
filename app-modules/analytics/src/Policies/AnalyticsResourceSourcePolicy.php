<?php

namespace AdvisingApp\Analytics\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\Analytics\Models\AnalyticsResourceSource;

class AnalyticsResourceSourcePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'analytics_resource_source.view-any',
            denyResponse: 'You do not have permission to view analytics resource sources.'
        );
    }

    public function view(Authenticatable $authenticatable, AnalyticsResourceSource $analyticsResourceSource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_source.*.view', "analytics_resource_source.{$analyticsResourceSource->id}.view"],
            denyResponse: 'You do not have permission to view this analytics resource source.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'analytics_resource_source.create',
            denyResponse: 'You do not have permission to create analytics resource sources.'
        );
    }

    public function update(Authenticatable $authenticatable, AnalyticsResourceSource $analyticsResourceSource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_source.*.update', "analytics_resource_source.{$analyticsResourceSource->id}.update"],
            denyResponse: 'You do not have permission to update this analytics resource source.'
        );
    }

    public function delete(Authenticatable $authenticatable, AnalyticsResourceSource $analyticsResourceSource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_source.*.delete', "analytics_resource_source.{$analyticsResourceSource->id}.delete"],
            denyResponse: 'You do not have permission to delete this analytics resource source.'
        );
    }

    public function restore(Authenticatable $authenticatable, AnalyticsResourceSource $analyticsResourceSource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_source.*.restore', "analytics_resource_source.{$analyticsResourceSource->id}.restore"],
            denyResponse: 'You do not have permission to restore this analytics resource source.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, AnalyticsResourceSource $analyticsResourceSource): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_source.*.force-delete', "analytics_resource_source.{$analyticsResourceSource->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this analytics resource source.'
        );
    }
}
