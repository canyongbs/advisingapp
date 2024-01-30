<?php

namespace AdvisingApp\Application\Policies;

use AdvisingApp\Application\Models\ApplicationSubmissionState;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ApplicationSubmissionStatePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['application_submission_state.view-any'],
            denyResponse: 'You do not have permission to view states.'
        );
    }

    public function view(Authenticatable $authenticatable, ApplicationSubmissionState $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['application_submission_state.*.view', "application_submission_state.{$model->id}.view"],
            denyResponse: 'You do not have permission to view this state.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'application_submission_state.create',
            denyResponse: 'You do not have permission to create states.'
        );
    }

    public function update(Authenticatable $authenticatable, ApplicationSubmissionState $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['application_submission_state.*.update', "application_submission_state.{$model->id}.update"],
            denyResponse: 'You do not have permission to update this state.'
        );
    }

    public function delete(Authenticatable $authenticatable, ApplicationSubmissionState $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['application_submission_state.*.delete', "application_submission_state.{$model->id}.delete"],
            denyResponse: 'You do not have permission to delete this state.'
        );
    }

    public function restore(Authenticatable $authenticatable, ApplicationSubmissionState $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['application_submission_state.*.restore', "application_submission_state.{$model->id}.restore"],
            denyResponse: 'You do not have permission to restore this state.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ApplicationSubmissionState $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['application_submission_state.*.force-delete', "application_submission_state.{$model->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this state.'
        );
    }
}
