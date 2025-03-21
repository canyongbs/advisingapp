<?php

use App\Features\StoreAnonymousNotifiableInformationFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        StoreAnonymousNotifiableInformationFeature::activate();
    }

    public function down(): void
    {
        StoreAnonymousNotifiableInformationFeature::deactivate();
    }
};
