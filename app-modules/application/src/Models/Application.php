<?php

namespace Assist\Application\Models;

use App\Models\BaseModel;
use Assist\Form\Enums\Rounding;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'embed_enabled',
        'allowed_domains',
        'is_wizard',
        'primary_color',
        'rounding',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
        'embed_enabled' => 'boolean',
        'allowed_domains' => 'array',
        'is_wizard' => 'boolean',
        'rounding' => Rounding::class,
    ];
}
