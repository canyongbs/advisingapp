<?php

namespace AdvisingApp\StudentDataModel\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\StudentDataModel\Database\Factories\EnrollmentSemesterFactory;
use AdvisingApp\StudentDataModel\Observers\EnrollmentSemesterObserver;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy(EnrollmentSemesterObserver::class)]
/**
 * @mixin IdeHelperEnrollmentSemester
 */
class EnrollmentSemester extends BaseModel implements Auditable
{
    /** @use HasFactory<EnrollmentSemesterFactory> */
    use HasFactory;

    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}
