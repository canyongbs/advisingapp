<?php

namespace AdvisingApp\Research\Models;

use Database\Factories\ResearchRequestQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchRequestQuestion extends Model
{
    /** @use HasFactory<ResearchRequestQuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
        'response',
    ];
}
