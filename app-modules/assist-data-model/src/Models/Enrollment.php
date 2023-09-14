<?php

namespace Assist\AssistDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

class Enrollment extends Model
{
    use HasFactory;
    use DefinesPermissions;

    public $incrementing = false;

    public $timestamps = false;
}
