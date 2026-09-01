<?php

use AdvisingApp\Theme\Settings\SettingsProperties\ThemeSettingsProperty;
use App\Features\ThemeLogoPublicDiskFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            $this->moveThemeLogos(fromDisk: 's3', toDisk: 's3-public');

            ThemeLogoPublicDiskFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            ThemeLogoPublicDiskFeature::deactivate();

            $this->moveThemeLogos(fromDisk: 's3-public', toDisk: 's3');
        });
    }

    private function moveThemeLogos(string $fromDisk, string $toDisk): void
    {
        Media::query()
            ->where('model_type', ThemeSettingsProperty::class)
            ->where('collection_name', 'logo')
            ->where('disk', $fromDisk)
            ->chunkById(100, function (Collection $mediaItems) use ($toDisk): void {
                $mediaItems->each(function (Media $media) use ($toDisk): void {
                    if (! $media->model instanceof HasMedia) {
                        return;
                    }

                    $media->move($media->model, 'logo', $toDisk, $media->file_name);
                });
            });
    }
};
