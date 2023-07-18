<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperEnrollment
 */
class Enrollment extends Model
{
    use HasFactory;

    protected $connection = 'sis';
}
