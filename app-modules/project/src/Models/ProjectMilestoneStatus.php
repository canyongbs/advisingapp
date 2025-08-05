<?php

namespace AdvisingApp\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMilestoneStatus extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectMilestoneStatusFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];
}
