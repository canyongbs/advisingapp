<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Enrollment
 *
 * @method static EnrollmentFactory factory($count = null, $state = [])
 * @method static Builder|Enrollment newModelQuery()
 * @method static Builder|Enrollment newQuery()
 * @method static Builder|Enrollment query()
 *
 * @mixin Eloquent
 */
class Enrollment extends Model
{
    use HasFactory;
}
