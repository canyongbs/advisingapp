<?php

use App\Features\TenantConfigEncryptionFeature;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use UnexpectedValueException;

return new class () extends Migration {
    public function up(): void
    {
        DB::connection('landlord')->transaction(function () {
            $encrypter = $this->makeLandlordEncrypter();

            DB::connection('landlord')
                ->table('tenants')
                ->whereNotNull('config')
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
