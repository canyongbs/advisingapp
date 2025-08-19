<?php

namespace AdvisingApp\Ai\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataAdvisor extends Model
{
    use SoftDeletes;

    protected $fillable = [];
}
