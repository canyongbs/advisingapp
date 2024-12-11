<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

return new class () extends Migration {
    public function up(): void
    {
        Media::query()
            ->where('model_type', 'user')
            ->where('collection_name', 'avatar')
            ->where('mime_type', 'application/json')
            ->get()
            ->each(function (Media $media) {
                $media->forceDelete();
            });
    }

    public function down(): void
    {
        // Not possible to revert
    }
};
