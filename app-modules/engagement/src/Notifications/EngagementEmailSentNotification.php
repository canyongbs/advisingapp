<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Notifications;

use App\Models\User;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use App\Notifications\MailMessage;
use Assist\Engagement\Models\Engagement;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification as FilamentNotification;

class EngagementEmailSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Engagement $engagement
    ) {}

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->emailTemplate($this->resolveEmailTemplate())
            ->subject('Your Engagement Email has successfully been delivered.')
            ->line("Your engagement was successfully delivered to {$this->engagement->recipient->display_name}.");
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->success()
            ->title('Engagement Email Successfully Delivered')
            ->body("Your engagement email was successfully delivered to {$this->engagement->recipient->display_name}.")
            ->getDatabaseMessage();
    }

    private function resolveEmailTemplate(): ?NotificationSetting
    {
        return $this->engagement->createdBy->teams()->first()?->division?->emailTemplate;
    }
}
