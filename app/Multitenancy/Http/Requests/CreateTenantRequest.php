<?php

/*
<COPYRIGHT>

    Copyright © 2022-2024, Canyon GBS LLC. All rights reserved.

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
