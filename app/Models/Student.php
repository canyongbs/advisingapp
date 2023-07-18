<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperStudent
 */
class Student extends Model
{
    use HasFactory;

    protected $connection = 'sis';

    protected $primaryKey = null;

    public $incrementing = false;

    public function cases(): MorphMany
    {
        return $this->morphMany(
            related: CaseItem::class,
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
            localKey: 'student_id'
        );
    }

    public function mcWhereHas($relation, $callback = null, $operator = '>=', $count = 1)
    {
        // TODO: Create a whereHas and other methods that work with the different database connections
    }
}
