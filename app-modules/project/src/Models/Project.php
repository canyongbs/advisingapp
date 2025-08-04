<?php

namespace AdvisingApp\Project\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Project\Database\Factories\ProjectFactory;
use AdvisingApp\Project\Observers\ProjectObserver;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([ProjectObserver::class])]
class Project extends BaseModel implements Auditable
{
  /** @use HasFactory<ProjectFactory> */
  use HasFactory;

  use SoftDeletes;
  use AuditableTrait;

  protected $fillable = [
    'name',
    'description',
  ];

  /**
   * @return MorphTo<Model, $this>
   */
  public function createdBy(): MorphTo
  {
    return $this->morphTo();
  }
}
