<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Assist\Engagement\Models\SmsTemplate;
use Illuminate\Auth\Access\Response;

class SmsTemplatePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'sms_template.view-any',
            denyResponse: 'You do not have permission to view sms templates.'
        );
    }

    public function view(User $user, SmsTemplate $smsTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['sms_template.*.view', "sms_template.{$smsTemplate->id}.view"],
            denyResponse: 'You do not have permission to view this sms template.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'sms_template.create',
            denyResponse: 'You do not have permission to create sms templates.'
        );
    }

    public function update(User $user, SmsTemplate $smsTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['sms_template.*.update', "sms_template.{$smsTemplate->id}.update"],
            denyResponse: 'You do not have permission to update this sms template.'
        );
    }

    public function delete(User $user, SmsTemplate $smsTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['sms_template.*.delete', "sms_template.{$smsTemplate->id}.delete"],
            denyResponse: 'You do not have permission to delete this sms template.'
        );
    }

    public function restore(User $user, SmsTemplate $smsTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['sms_template.*.restore', "sms_template.{$smsTemplate->id}.restore"],
            denyResponse: 'You do not have permission to restore this sms template.'
        );
    }

    public function forceDelete(User $user, SmsTemplate $smsTemplate): Response
    {
        return $user->canOrElse(
            abilities: ['sms_template.*.force-delete', "sms_template.{$smsTemplate->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this sms template.'
        );
    }
}
