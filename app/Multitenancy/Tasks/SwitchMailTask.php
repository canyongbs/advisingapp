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

namespace App\Multitenancy\Tasks;

use AdvisingApp\Notification\Notifications\ChannelManager;
use AdvisingApp\Notification\Notifications\Channels\MailChannel;
use App\Multitenancy\DataTransferObjects\TenantMailConfig;
use Illuminate\Contracts\Mail\Factory;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Mailer as MailMailer;
use Illuminate\Mail\MailManager;
use Illuminate\Notifications\ChannelManager as BaseChannelManager;
use Illuminate\Notifications\Channels\MailChannel as BaseMailChannel;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchMailTask implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalFromAddress = null,
        protected ?string $originalFromName = null,
    ) {
        $this->originalFromAddress ??= config('mail.from.address');
        $this->originalFromName ??= config('mail.from.name');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        /** @var TenantMailConfig $config */
        $config = $tenant->config->mail;

        preg_match('/^(.+)\.[^.]+\.[^.]+$/', $tenant->domain, $matches);

        $subDomainBasedEmail = $matches[1] . '@' . config('mail.from.root_domain');

        $this->setMailConfig(
            fromAddress: $subDomainBasedEmail,
            fromName: $config->fromName,
        );
    }

    public function forgetCurrent(): void
    {
        $this->setMailConfig(
            fromAddress: $this->originalFromAddress,
            fromName: $this->originalFromName,
        );
    }

    protected function setMailConfig(
        ?string $fromAddress = null,
        ?string $fromName = null,
    ): void {
        config(
            [
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
            ]
        );

        app()->forgetInstance('mailer');
        app()->forgetInstance('mail.manager');
        app()->forgetInstance(Mailer::class);
        app()->forgetInstance(MailMailer::class);
        app()->forgetInstance(MailManager::class);
        app()->forgetInstance(Factory::class);
        app()->forgetInstance(BaseChannelManager::class);
        app()->forgetInstance(ChannelManager::class);
        app()->forgetInstance(BaseMailChannel::class);
        app()->forgetInstance(MailChannel::class);
    }
}
