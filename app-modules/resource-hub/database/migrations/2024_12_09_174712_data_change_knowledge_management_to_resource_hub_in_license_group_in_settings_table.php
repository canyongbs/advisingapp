<?php

use App\DataTransferObjects\LicenseManagement\LicenseData;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends Migration
{
    public function up(): void
    {
        $payload = DB::table('settings')->where('group', 'license')
            ->where('name', 'data')
            ->value('payload');

        $payload = decrypt(json_decode($payload));
        $payload['addons']['resource_hub'] = $payload['addons']['knowledge_management'];
        unset($payload['addons']['knowledge_management']);

        DB::table('settings')->where('group', 'license')
            ->where('name', 'data')
            ->update(['payload' => encrypt(json_encode($payload))]);
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
