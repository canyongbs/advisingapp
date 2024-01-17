<?php

namespace App\Multitenancy\Tasks;

use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchMailTask implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalMailer = null,
        protected ?string $originalFromAddress = null,
        protected ?string $originalFromName = null,
        protected ?string $originalSmtpHost = null,
        protected ?int $originalSmtpPort = null,
        protected ?string $originalSmtpEncryption = null,
        protected ?string $originalSmtpUsername = null,
        protected ?string $originalSmtpPassword = null,
        protected ?int $originalSmtpTimeout = null,
        protected ?string $originalSmtpLocalDomain = null,
    ) {
        $this->originalMailer ??= config('mail.default');
        $this->originalFromAddress ??= config('mail.from.address');
        $this->originalFromName ??= config('mail.from.name');
        $this->originalSmtpHost ??= config('mail.mailers.smtp.host');
        $this->originalSmtpPort ??= config('mail.mailers.smtp.port');
        $this->originalSmtpEncryption ??= config('mail.mailers.smtp.encryption');
        $this->originalSmtpUsername ??= config('mail.mailers.smtp.username');
        $this->originalSmtpPassword ??= config('mail.mailers.smtp.password');
        $this->originalSmtpTimeout ??= config('mail.mailers.smtp.timeout');
        $this->originalSmtpLocalDomain ??= config('mail.mailers.smtp.local_domain');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        $config = $tenant->config;

        $this->setMailConfig(
            mailer: $config->mail->mailer,
            fromAddress: $config->mail->fromAddress,
            fromName: $config->mail->fromName,
            smtpHost: $config->mail->mailers->smtp->host,
            smtpPort: $config->mail->mailers->smtp->port,
            smtpEncryption: $config->mail->mailers->smtp->encryption,
            smtpUsername: $config->mail->mailers->smtp->username,
            smtpPassword: $config->mail->mailers->smtp->password,
            smtpTimeout: $config->mail->mailers->smtp->timeout,
            smtpLocalDomain: $config->mail->mailers->smtp->localDomain,
        );
    }

    public function forgetCurrent(): void
    {
        $this->setMailConfig(
            mailer: $this->originalMailer,
            fromAddress: $this->originalFromAddress,
            fromName: $this->originalFromName,
            smtpHost: $this->originalSmtpHost,
            smtpPort: $this->originalSmtpPort,
            smtpEncryption: $this->originalSmtpEncryption,
            smtpUsername: $this->originalSmtpUsername,
            smtpPassword: $this->originalSmtpPassword,
            smtpTimeout: $this->originalSmtpTimeout,
            smtpLocalDomain: $this->originalSmtpLocalDomain,
        );
    }

    protected function setMailConfig(
        ?string $mailer = null,
        ?string $fromAddress = null,
        ?string $fromName = null,
        ?string $smtpHost = null,
        ?int $smtpPort = null,
        ?string $smtpEncryption = null,
        ?string $smtpUsername = null,
        ?string $smtpPassword = null,
        ?int $smtpTimeout = null,
        ?string $smtpLocalDomain = null,
    ): void {
        config(
            [
                'mail.default' => $mailer,
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
                'mail.mailers.smtp.host' => $smtpHost,
                'mail.mailers.smtp.port' => $smtpPort,
                'mail.mailers.smtp.encryption' => $smtpEncryption,
                'mail.mailers.smtp.username' => $smtpUsername,
                'mail.mailers.smtp.password' => $smtpPassword,
                'mail.mailers.smtp.timeout' => $smtpTimeout,
                'mail.mailers.smtp.local_domain' => $smtpLocalDomain,
            ]
        );
    }
}
