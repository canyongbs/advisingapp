<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class EngagementFile extends BaseModel
{
    protected $fillable = [
        'description',
    ];

    public function student(): MorphToMany
    {
        return $this->morphedByMany(
            related: Student::class,
            name: 'engagement_file_entity',
        );
    }

    public function prospect(): MorphToMany
    {
        return $this->morphedByMany(Prospect::class, 'engagement_file_entity');
    }
}
