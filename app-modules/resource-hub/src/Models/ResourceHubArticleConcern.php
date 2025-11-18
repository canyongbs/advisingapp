<?php

namespace AdvisingApp\ResourceHub\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\ResourceHub\Enums\ConcernStatus;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperResourceHubArticleConcern
 */
class ResourceHubArticleConcern extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;

    protected $fillable = [
        'description',
        'created_by_id',
        'status',
        'resource_hub_article_id',
    ];

    protected $casts = [
        'status' => ConcernStatus::class,
    ];
}
