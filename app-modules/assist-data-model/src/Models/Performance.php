<?php

namespace Assist\AssistDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * Assist\AssistDataModel\Models\Performance
 *
 * @method static \Assist\AssistDataModel\Database\Factories\PerformanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Performance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Performance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Performance query()
 *
 * @mixin \Eloquent
 */
class Performance extends Model
{
    use HasFactory;
    use DefinesPermissions;

    protected $table = 'performance';

    // TODO: Need to revisit whether or not this should be the primary key, just using it for now since there is nothing else
    protected $primaryKey = 'sisid';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
