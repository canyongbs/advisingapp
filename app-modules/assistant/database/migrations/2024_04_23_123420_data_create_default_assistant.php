<?php

use Illuminate\Database\Migrations\Migration;
use Database\Migrations\Concerns\CanModifySettings;

return new class () extends Migration {
    use CanModifySettings;

    public function up(): void
    {
        $this->updateSettings(
            group: 'license',
            name: 'data',
            modifyPayload: function (array $payload): array {
                $payload['addons']['customAiAssistants'] = true;

                return $payload;
            },
            isEncrypted: true,
        );
    }
};
