<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * App\Models\Program
 *
 * @method static \Database\Factories\ProgramFactory factory($count = null, $state = [])
 * @method static Builder|Program newModelQuery()
 * @method static Builder|Program newQuery()
 * @method static Builder|Program query()
 *
 * @mixin Eloquent
 * @mixin IdeHelperProgram
 */
class Program extends Model
{
    use HasFactory;
    use DefinesPermissions;
}
