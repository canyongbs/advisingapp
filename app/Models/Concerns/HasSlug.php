<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    public static function bootHasSlug()
    {
        static::saving(function ($model) {
            static::setSlug($model);
        });
    }

    public static function setSlug(Model $model): void
    {
        if (is_null($model->slug)) {
            $model->slug = Str::slug($model->getSlugProperty(), '-');
        }
    }

    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }

    public function getSlugProperty(): string
    {
        return $this->name;
    }
}
