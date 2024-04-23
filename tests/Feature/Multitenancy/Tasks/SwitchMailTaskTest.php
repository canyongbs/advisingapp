<?php

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

    assertEquals($after, [
        'mail.default' => $tenant->config->mail->mailer,
        'mail.from.address' => $tenant->config->mail->fromAddress,
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
