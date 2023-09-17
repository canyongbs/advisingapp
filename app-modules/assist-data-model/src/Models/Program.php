<?php

namespace Assist\AssistDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * Assist\AssistDataModel\Models\Program
 *
 * @method static \Assist\AssistDataModel\Database\Factories\ProgramFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Program query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperProgram
 */
class Program extends Model
{
    use HasFactory;
    use DefinesPermissions;

    // TODO: Need to revisit whether or not this should be the primary key, just using it for now since there is nothing else
    protected $primaryKey = 'sisid';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
