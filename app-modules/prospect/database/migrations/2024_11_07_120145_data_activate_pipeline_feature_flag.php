<?php

use App\Features\PipelineFlag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        PipelineFlag::activate();
    }

    public function down(): void
    {
        PipelineFlag::deactivate();
    }
};
