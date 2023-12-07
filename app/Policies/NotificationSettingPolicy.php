<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NotificationSetting;
use Illuminate\Auth\Access\Response;

class NotificationSettingPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'notification_setting.view-any',
            denyResponse: 'You do not have permission to view notification settings.'
        );
    }

    public function view(User $user, NotificationSetting $notificationSetting): Response
    {
        return $user->canOrElse(
            abilities: ['notification_setting.*.view', "notification_setting.{$notificationSetting->id}.view"],
            denyResponse: 'You do not have permission to view this notification setting.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'notification_setting.create',
            denyResponse: 'You do not have permission to create notification settings.'
        );
    }

    public function update(User $user, NotificationSetting $notificationSetting): Response
    {
        return $user->canOrElse(
            abilities: ['notification_setting.*.update', "notification_setting.{$notificationSetting->id}.update"],
            denyResponse: 'You do not have permission to update this notification setting.'
        );
    }

    public function delete(User $user, NotificationSetting $notificationSetting): Response
    {
        return $user->canOrElse(
            abilities: ['notification_setting.*.delete', "notification_setting.{$notificationSetting->id}.delete"],
            denyResponse: 'You do not have permission to delete this notification setting.'
        );
    }

    public function restore(User $user, NotificationSetting $notificationSetting): Response
    {
        return $user->canOrElse(
            abilities: ['notification_setting.*.restore', "notification_setting.{$notificationSetting->id}.restore"],
            denyResponse: 'You do not have permission to restore this notification setting.'
        );
    }

    public function forceDelete(User $user, NotificationSetting $notificationSetting): Response
    {
        return $user->canOrElse(
            abilities: ['notification_setting.*.force-delete', "notification_setting.{$notificationSetting->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this notification setting.'
        );
    }
}
