<?php

namespace Assist\Form\Policies;

use App\Models\User;
use Assist\Form\Models\Form;
use Illuminate\Auth\Access\Response;

class FormPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'form.view-any',
            denyResponse: 'You do not have permission to view forms.'
        );
    }

    public function view(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.view', "form.{$form->id}.view"],
            denyResponse: 'You do not have permission to view this form.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'form.create',
            denyResponse: 'You do not have permission to create forms.'
        );
    }

    public function update(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.update', "form.{$form->id}.update"],
            denyResponse: 'You do not have permission to update this form.'
        );
    }

    public function delete(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.delete', "form.{$form->id}.delete"],
            denyResponse: 'You do not have permission to delete this form.'
        );
    }

    public function restore(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.restore', "form.{$form->id}.restore"],
            denyResponse: 'You do not have permission to restore this form.'
        );
    }

    public function forceDelete(User $user, Form $form): Response
    {
        return $user->canOrElse(
            abilities: ['form.*.force-delete', "form.{$form->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this form.'
        );
    }
}
