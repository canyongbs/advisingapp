<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $connection = 'sis';

    public function cases(): MorphMany
    {
        return $this->morphMany(CaseItem::class, 'respondent');
    }
}
