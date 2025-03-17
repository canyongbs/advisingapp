<?php

use App\Features\EmailSignature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        EmailSignature::activate();
    }

    public function down(): void
    {
        EmailSignature::deactivate();
    }
};
