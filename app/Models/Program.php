<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperProgram
 */
class Program extends Model
{
    use HasFactory;

    protected $connection = 'sis';
}
