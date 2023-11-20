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
        return Response::deny('Inbound webhooks cannot be created.');
    }

    public function update(User $user, InboundWebhook $inboundWebhook): Response
    {
        return Response::deny('Inbound webhooks cannot be updated.');
    }

    public function delete(User $user, InboundWebhook $inboundWebhook): Response
    {
        return Response::deny('Inbound webhooks cannot be deleted.');
    }

    public function restore(User $user, InboundWebhook $inboundWebhook): Response
    {
        return Response::deny('Inbound webhooks cannot be restored.');
    }

    public function forceDelete(User $user, InboundWebhook $inboundWebhook): Response
    {
        return Response::deny('Inbound webhooks cannot be force deleted.');
    }
}
