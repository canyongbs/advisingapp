<?php

namespace FilamentTiptapEditor\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use FilamentTiptapEditor\Tests\Database\Factories\PageFactory;

class Page extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'json_content' => 'array',
    ];

    protected static function newFactory(): PageFactory
    {
        return new PageFactory();
    }
}
