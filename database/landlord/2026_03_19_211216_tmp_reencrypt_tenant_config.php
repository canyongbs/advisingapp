<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use App\Features\TenantConfigEncryptionFeature;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::connection('landlord')->transaction(function () {
            $encrypter = $this->makeLandlordEncrypter();

            DB::connection('landlord')
                ->table('tenants')
                ->whereNotNull('config')
                ->orderBy('id')
                ->each(function (object $tenant) use ($encrypter) {
                    $decrypted = $encrypter->decrypt($tenant->config);

                    if (! ($decrypted instanceof TenantConfig)) {
                        throw new UnexpectedValueException("Decrypted tenant config is not an instance of TenantConfig for tenant ID {$tenant->id}");
                    }

                    DB::connection('landlord')
                        ->table('tenants')
                        ->where('id', $tenant->id)
                        ->update(['config' => Crypt::encryptString($decrypted->toJson())]);
                });

            TenantConfigEncryptionFeature::activate();
        });
    }

    public function down(): void
    {
        DB::connection('landlord')->transaction(function () {
            TenantConfigEncryptionFeature::deactivate();

            $encrypter = $this->makeLandlordEncrypter();

            DB::connection('landlord')
                ->table('tenants')
                ->whereNotNull('config')
                ->orderBy('id')
                ->each(function (object $tenant) use ($encrypter) {
                    $json = Crypt::decryptString($tenant->config);

                    $tenantConfig = TenantConfig::from(json_decode($json, true));

                    DB::connection('landlord')
                        ->table('tenants')
                        ->where('id', $tenant->id)
                        ->update(['config' => $encrypter->encrypt($tenantConfig)]);
                });
        });
    }

    private function makeLandlordEncrypter(): Encrypter
    {
        $key = config('app.key');

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(Str::after($key, 'base64:'));
        }

        return new Encrypter($key, config('app.cipher'));
    }
};
