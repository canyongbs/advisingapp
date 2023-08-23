<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * App\Models\Enrollment
 *
 * @method static \Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static Builder|Enrollment newModelQuery()
 * @method static Builder|Enrollment newQuery()
 * @method static Builder|Enrollment query()
 *
 * @mixin Eloquent
 */
class Enrollment extends Model
{
    use HasFactory;
    use DefinesPermissions;
}
