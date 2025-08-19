<?php

namespace AdvisingApp\Ai\Policies;

use AdvisingApp\Ai\Models\DataAdvisor;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class DataAdvisorPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'ai-data-advisors.view-any',
            denyResponse: 'You do not have permission to view data advisors.'
        );
    }

    public function view(Authenticatable $authenticatable, DataAdvisor $dataAdvisor): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["ai-data-advisors.{$dataAdvisor->getKey()}.view"],
            denyResponse: 'You do not have permission to view data advisors.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'ai-data-advisors.create',
            denyResponse: 'You do not have permission to create data advisors.'
        );
    }

    public function update(Authenticatable $authenticatable, DataAdvisor $dataAdvisor): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["ai-data-advisors.{$dataAdvisor->getKey()}.update"],
            denyResponse: 'You do not have permission to create data advisors.'
        );
    }

    public function delete(Authenticatable $authenticatable, DataAdvisor $dataAdvisor): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["ai-data-advisors.{$dataAdvisor->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this data advisor.'
        );
    }

    public function restore(Authenticatable $authenticatable, DataAdvisor $dataAdvisor): Response
    {
         return $authenticatable->canOrElse(
            abilities: ["ai-data-advisors.{$dataAdvisor->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this data advisor.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, DataAdvisor $dataAdvisor): Response
    {
         return $authenticatable->canOrElse(
            abilities: ["ai-data-advisors.{$dataAdvisor->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this data advisor.'
        );
    }
}
