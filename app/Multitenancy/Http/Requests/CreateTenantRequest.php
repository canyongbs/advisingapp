<?php

namespace App\Multitenancy\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:landlord.tenants,domain', 'regex:/^((?:[a-zA-Z0-9-]*?\.)?[a-zA-Z0-9-]+\.[a-zA-Z]{2,})$/i'],
            'database.host' => ['required', 'string', 'max:255'],
            'database.port' => ['required', 'integer', 'min:1', 'max:65535'],
            'database.database' => ['required', 'string', 'max:255'],
            'database.username' => ['required', 'string', 'max:255'],
            'database.password' => ['required', 'string', 'max:255'],
            'sis_database.host' => ['required', 'string', 'max:255'],
            'sis_database.port' => ['required', 'integer', 'min:1', 'max:65535'],
            'sis_database.database' => ['required', 'string', 'max:255'],
            'sis_database.username' => ['required', 'string', 'max:255'],
            'sis_database.password' => ['required', 'string', 'max:255'],
            's3_filesystem.key' => ['required', 'string', 'max:255'],
            's3_filesystem.secret' => ['required', 'string', 'max:255'],
            's3_filesystem.region' => ['required', 'string', 'max:255'],
            's3_filesystem.bucket' => ['required', 'string', 'max:255'],
            's3_filesystem.url' => ['nullable', 'string', 'max:255'],
            's3_filesystem.endpoint' => ['nullable', 'string', 'max:255'],
            's3_filesystem.use_path_style_endpoint' => ['nullable', 'boolean'],
            's3_filesystem.throw' => ['nullable', 'boolean'],
            's3_filesystem.root' => ['nullable', 'string', 'max:255'],
            's3_public_filesystem.key' => ['required', 'string', 'max:255'],
            's3_public_filesystem.secret' => ['required', 'string', 'max:255'],
            's3_public_filesystem.region' => ['required', 'string', 'max:255'],
            's3_public_filesystem.bucket' => ['required', 'string', 'max:255'],
            's3_public_filesystem.url' => ['nullable', 'string', 'max:255'],
            's3_public_filesystem.endpoint' => ['nullable', 'string', 'max:255'],
            's3_public_filesystem.use_path_style_endpoint' => ['nullable', 'boolean'],
            's3_public_filesystem.throw' => ['nullable', 'boolean'],
            's3_public_filesystem.root' => ['nullable', 'string', 'max:255'],
            'mail.mailer' => ['nullable', 'string', 'max:255'],
            'mail.from_address' => ['required', 'string', 'max:255'],
            'mail.from_name' => ['required', 'string', 'max:255'],
            'mail.mailers.smtp.host' => ['required', 'string', 'max:255'],
            'mail.mailers.smtp.port' => ['required', 'integer', 'min:1', 'max:65535'],
            'mail.mailers.smtp.encryption' => ['nullable', 'string', 'max:255'],
            'mail.mailers.smtp.username' => ['nullable', 'string', 'max:255'],
            'mail.mailers.smtp.password' => ['nullable', 'string', 'max:255'],
            'mail.mailers.smtp.timeout' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail.mailers.smtp.local_domain' => ['nullable', 'string', 'max:255'],
        ];
    }
}
