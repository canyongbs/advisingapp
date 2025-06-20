<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('ai-qna-advisor.allow_selection_of_model', true);
        $this->migrator->add('ai-qna-advisor.preselected_model', null);
        $this->migrator->add('ai-qna-advisor.instructions', null);
        $this->migrator->add('ai-qna-advisor.background_information', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai-qna-advisor.allow_selection_of_model');
        $this->migrator->deleteIfExists('ai-qna-advisor.preselected_model');
        $this->migrator->deleteIfExists('ai-qna-advisor.instructions');
        $this->migrator->deleteIfExists('ai-qna-advisor.background_information');
    }
};
