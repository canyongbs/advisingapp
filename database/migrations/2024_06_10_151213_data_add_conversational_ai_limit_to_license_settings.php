<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifySettings;

return new class () extends Migration {
    use CanModifySettings;

    public function up(): void
    {
        $this->updateSettings('license', 'data', function (array $data): array {
            $data['limits']['conversationalAiAssistants'] = 0;

            return $data;
        }, isEncrypted: true);
    }

    public function down(): void
    {
        $this->updateSettings('license', 'data', function (array $data): array {
            unset($data['limits']['conversationalAiAssistants']);

            return $data;
        }, isEncrypted: true);
    }
};
