<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailTemplate extends BaseModel implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'primary_color',
        'related_to_type',
        'related_to_id',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile();
    }

    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }
}
