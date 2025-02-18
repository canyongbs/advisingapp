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

use App\Models\Tenant;

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('switches the mail configs', function () {
    $before = config()->getMany([
        'mail.default',
        'mail.from.address',
        'mail.from.name',
        'mail.mailers.smtp.host',
        'mail.mailers.smtp.port',
        'mail.mailers.smtp.encryption',
        'mail.mailers.smtp.username',
        'mail.mailers.smtp.password',
        'mail.mailers.smtp.timeout',
        'mail.mailers.smtp.local_domain',
    ]);

    $tenant = Tenant::first()->makeCurrent();

    $after = config()->getMany([
        'mail.default',
        'mail.from.address',
        'mail.from.name',
        'mail.mailers.smtp.host',
        'mail.mailers.smtp.port',
        'mail.mailers.smtp.encryption',
        'mail.mailers.smtp.username',
        'mail.mailers.smtp.password',
        'mail.mailers.smtp.timeout',
        'mail.mailers.smtp.local_domain',
    ]);

    preg_match('/^(.+)\.[^.]+\.[^.]+$/', $tenant->domain, $matches);

    $subDomainBasedEmail = $matches[1] . '@' . config('mail.from.root_domain');

    assertEquals($after, [
        'mail.default' => $tenant->config->mail->mailer,
        'mail.from.address' => $subDomainBasedEmail,
        'mail.from.name' => $tenant->config->mail->fromName,
        'mail.mailers.smtp.host' => $tenant->config->mail->mailers->smtp->host,
        'mail.mailers.smtp.port' => $tenant->config->mail->mailers->smtp->port,
        'mail.mailers.smtp.encryption' => $tenant->config->mail->mailers->smtp->encryption,
        'mail.mailers.smtp.username' => $tenant->config->mail->mailers->smtp->username,
        'mail.mailers.smtp.password' => $tenant->config->mail->mailers->smtp->password,
        'mail.mailers.smtp.timeout' => $tenant->config->mail->mailers->smtp->timeout,
        'mail.mailers.smtp.local_domain' => $tenant->config->mail->mailers->smtp->localDomain,
    ]);

    Tenant::forgetCurrent();

    $after = config()->get([
        'mail.default',
        'mail.from.address',
        'mail.from.name',
        'mail.mailers.smtp.host',
        'mail.mailers.smtp.port',
        'mail.mailers.smtp.encryption',
        'mail.mailers.smtp.username',
        'mail.mailers.smtp.password',
        'mail.mailers.smtp.timeout',
        'mail.mailers.smtp.local_domain',
    ]);

    assertEquals($before, $after);
});
