<?php

namespace AdvisingApp\Alert\Policies;

use AdvisingApp\Alert\Models\AlertStatus;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class AlertStatusPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'alert_status.view-any',
            denyResponse: 'You do not have permission to view alert statuses.'
        );
    }

    public function view(Authenticatable $authenticatable, AlertStatus $alertStatus): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["alert_status.{$alertStatus->getKey()}.view"],
            denyResponse: 'You do not have permission to view this alert status.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'alert_status.create',
            denyResponse: 'You do not have permission to create alert statuses.'
        );
    }

    public function update(Authenticatable $authenticatable, AlertStatus $alertStatus): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["alert_status.{$alertStatus->getKey()}.update"],
            denyResponse: 'You do not have permission to update this alert status.'
        );
    }

    public function delete(Authenticatable $authenticatable, AlertStatus $alertStatus): Response
    {
        if (count($alertStatus->alerts) > 0) {
            return Response::deny('You cannot delete this alert status because this status is associated with active alerts.');
        }

        return $authenticatable->canOrElse(
            abilities: ["alert_status.{$alertStatus->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this alert status.'
        );
    }

    public function restore(Authenticatable $authenticatable, AlertStatus $alertStatus): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["alert_status.{$alertStatus->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this alert status.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, AlertStatus $alertStatus): Response
    {
        if (count($alertStatus->alerts) > 0) {
            return Response::deny('You cannot delete this alert status because this status is associated with active alerts.');
        }

        return $authenticatable->canOrElse(
            abilities: ["alert_status.{$alertStatus->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this alert status.'
        );
    }
}
