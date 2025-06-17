<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('ai-qna-advisor.instructions', null);
        $this->migrator->add('ai-qna-advisor.background_information', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai-qna-advisor.instructions');
        $this->migrator->deleteIfExists('ai-qna-advisor.background_information');
    }
};
