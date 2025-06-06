<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Notification\Notifications\Messages;

use App\Models\NotificationSetting;
use Illuminate\Notifications\Messages\MailMessage as BaseMailMessage;

class MailMessage extends BaseMailMessage
{
    protected ?string $recipientEmailAddress = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function to(?string $recipientEmailAddress): self
    {
        $this->recipientEmailAddress = $recipientEmailAddress;

        return $this;
    }

    public function getRecipientEmailAddress(): ?string
    {
        return $this->recipientEmailAddress;
    }

    public function content(string $content): static
    {
        $this->viewData = [
            $this->viewData,
            'content' => $content,
        ];

        return $this;
    }

    public function settings(?NotificationSetting $setting): static
    {
        if (! empty($setting->from_name)) {
            $this->from(
                address: config('mail.from.address'),
                name: $setting->from_name,
            );
        }

        $this->viewData = [
            $this->viewData,
            'settings' => $setting,
        ];

        return $this;
    }

    public function toArray(): array
    {
        return [
            'recipient_email_address' => $this->getRecipientEmailAddress(),
            'level' => $this->level,
            'subject' => (string) $this->subject,
            'greeting' => $this->greeting,
            'salutation' => $this->salutation,
            'introLines' => $this->introLines,
            'outroLines' => $this->outroLines,
            'actionText' => $this->actionText,
            'actionUrl' => $this->actionUrl,
            'displayableActionUrl' => str_replace(['mailto:', 'tel:'], '', $this->actionUrl ?? ''),
            'viewData' => $this->viewData,
        ];
    }
}
