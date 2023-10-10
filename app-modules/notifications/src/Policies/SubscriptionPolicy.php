<?php

namespace Assist\Notifications\Policies;

use App\Models\User;
use Assist\Notifications\Models\Subscription;
use Illuminate\Auth\Access\Response;

class SubscriptionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'subscription.view-any',
            denyResponse: 'You do not have permission to view subscriptions.'
        );
    }

    public function view(User $user, Subscription $subscription): Response
    {
        return $user->canOrElse(
            abilities: ['subscription.*.view', "subscription.{$subscription->id}.view"],
            denyResponse: 'You do not have permission to view this subscription.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'subscription.create',
            denyResponse: 'You do not have permission to create subscriptions.'
        );
    }

    public function update(User $user, Subscription $subscription): Response
    {
        return $user->canOrElse(
            abilities: ['subscription.*.update', "subscription.{$subscription->id}.update"],
            denyResponse: 'You do not have permission to update this subscription.'
        );
    }

    public function delete(User $user, Subscription $subscription): Response
    {
        return $user->canOrElse(
            abilities: ['subscription.*.delete', "subscription.{$subscription->id}.delete"],
            denyResponse: 'You do not have permission to delete this subscription.'
        );
    }

    public function restore(User $user, Subscription $subscription): Response
    {
        return $user->canOrElse(
            abilities: ['subscription.*.restore', "subscription.{$subscription->id}.restore"],
            denyResponse: 'You do not have permission to restore this subscription.'
        );
    }

    public function forceDelete(User $user, Subscription $subscription): Response
    {
        return $user->canOrElse(
            abilities: ['subscription.*.force-delete', "subscription.{$subscription->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this subscription.'
        );
    }
}
