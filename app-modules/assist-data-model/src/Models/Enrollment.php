<?php

namespace Assist\AssistDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * Assist\AssistDataModel\Models\Enrollment
 *
 * @method static \Assist\AssistDataModel\Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperEnrollment
 */
class Enrollment extends Model
{
    use HasFactory;
    use DefinesPermissions;

    // TODO: Need to revisit whether or not this should be the primary key, just using it for now since there is nothing else
    protected $primaryKey = 'sisid';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
