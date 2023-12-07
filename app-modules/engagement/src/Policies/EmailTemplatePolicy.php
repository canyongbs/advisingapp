<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Assist\Engagement\Models\EmailTemplate;
use Illuminate\Auth\Access\Response;

class EmailTemplatePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'email_template.view-any',
            denyResponse: 'You do not have permission to view email templates.'
        );
    }

    public function view(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.view', "email_template.{$emailTemplate->id}.view"],
            denyResponse: 'You do not have permission to view this email template.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'email_template.create',
            denyResponse: 'You do not have permission to create email templates.'
        );
    }

    public function update(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.update', "email_template.{$emailTemplate->id}.update"],
            denyResponse: 'You do not have permission to update this email template.'
        );
    }

    public function delete(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.delete', "email_template.{$emailTemplate->id}.delete"],
            denyResponse: 'You do not have permission to delete this email template.'
        );
    }

    public function restore(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.restore', "email_template.{$emailTemplate->id}.restore"],
            denyResponse: 'You do not have permission to restore this email template.'
        );
    }

    public function forceDelete(User $user, EmailTemplate $emailTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['email_template.*.force-delete', "email_template.{$emailTemplate->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this email template.'
        );
    }
}
