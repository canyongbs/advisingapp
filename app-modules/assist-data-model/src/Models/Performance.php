<?php

namespace Assist\AssistDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

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
