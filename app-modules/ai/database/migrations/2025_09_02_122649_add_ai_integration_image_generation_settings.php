<?php

use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    /**
     * @var array<string>
     * */
    private array $properties = [
        'ai.open_ai_gpt_4o_image_generation_deployment',
        'ai.open_ai_gpt_4o_image_generation_deployment',
        'ai.open_ai_gpt_o3_image_generation_deployment',
        'ai.open_ai_gpt_41_mini_image_generation_deployment',
        'ai.open_ai_gpt_41_nano_image_generation_deployment',
        'ai.open_ai_gpt_o4_mini_image_generation_deployment',
        'ai.open_ai_gpt_5_image_generation_deployment',
        'ai.open_ai_gpt_5_mini_image_generation_deployment',
        'ai.open_ai_gpt_5_nano_image_generation_deployment',
    ];

    public function up(): void
    {
        foreach ($this->properties as $property) {
            try {
                $this->migrator->add($property, encrypted: true);
            } catch (SettingAlreadyExists $exception) {
                // Do nothing
            }
        }
    }

    public function down(): void
    {
        foreach ($this->properties as $property) {
            $this->migrator->deleteIfExists($property);
        }
    }
};
