<?php

namespace App\Models;

use Eloquent;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Program
 *
 * @method static ProgramFactory factory($count = null, $state = [])
 * @method static Builder|Program newModelQuery()
 * @method static Builder|Program newQuery()
 * @method static Builder|Program query()
 *
 * @mixin Eloquent
 */
class Program extends Model
{
    use HasFactory;
}
