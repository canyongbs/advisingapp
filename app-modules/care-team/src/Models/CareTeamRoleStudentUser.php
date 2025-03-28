<?php

namespace AdvisingApp\CareTeam\Models;

use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CareTeamRoleStudentUser extends Pivot
{
    use HasUuids;
    use HasFactory;

    public function careTeamRole(): BelongsTo
    {
        return $this->belongsTo(CareTeamRole::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
