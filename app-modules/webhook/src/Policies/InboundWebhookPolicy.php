<?php

namespace Assist\Webhook\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Webhook\Models\InboundWebhook;

class InboundWebhookPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'inbound_webhook.view-any',
            denyResponse: 'You do not have permission to view inbound webhooks.'
        );
    }

    public function view(User $user, InboundWebhook $inboundWebhook): Response
    {
        return $user->canOrElse(
            abilities: ['inbound_webhook.*.view', "inbound_webhook.{$inboundWebhook->id}.view"],
            denyResponse: 'You do not have permission to view this inbound webhook.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'inbound_webhook.create',
            denyResponse: 'You do not have permission to create inbound webhooks.'
        );
    }

    public function update(User $user, InboundWebhook $inboundWebhook): Response
    {
        return $user->canOrElse(
            abilities: ['inbound_webhook.*.update', "inbound_webhook.{$inboundWebhook->id}.update"],
            denyResponse: 'You do not have permission to update this inbound webhook.'
        );
    }

    public function delete(User $user, InboundWebhook $inboundWebhook): Response
    {
        return $user->canOrElse(
            abilities: ['inbound_webhook.*.delete', "inbound_webhook.{$inboundWebhook->id}.delete"],
            denyResponse: 'You do not have permission to delete this inbound webhook.'
        );
    }

    public function restore(User $user, InboundWebhook $inboundWebhook): Response
    {
        return $user->canOrElse(
            abilities: ['inbound_webhook.*.restore', "inbound_webhook.{$inboundWebhook->id}.restore"],
            denyResponse: 'You do not have permission to restore this inbound webhook.'
        );
    }

    public function forceDelete(User $user, InboundWebhook $inboundWebhook): Response
    {
        return $user->canOrElse(
            abilities: ['inbound_webhook.*.force-delete', "inbound_webhook.{$inboundWebhook->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this inbound webhook.'
        );
    }
}
