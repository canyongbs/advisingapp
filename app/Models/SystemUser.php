<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

class SystemUser extends Authenticatable implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;
    use HasApiTokens;

    protected $fillable = [
        'name',
    ];
}
