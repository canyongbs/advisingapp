<?php

namespace AdvisingApp\CaseManagement\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\CaseManagement\Database\Factories\CaseTypeEmailTemplateFactory;
use AdvisingApp\CaseManagement\Enums\CaseEmailTemplateType;
use AdvisingApp\CaseManagement\Enums\CaseTypeEmailTemplateRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperCaseTypeEmailTemplate
 */
class CaseTypeEmailTemplate extends Model implements Auditable
{
    /** @use HasFactory<CaseTypeEmailTemplateFactory> */
    use HasFactory;

    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'case_type_id',
        'type',
        'subject',
        'body',
        'role',
    ];

    protected $casts = [
        'subject' => 'array',
        'body' => 'array',
        'type' => CaseEmailTemplateType::class,
        'role' => CaseTypeEmailTemplateRole::class,
    ];

    /**
     * @return BelongsTo<CaseType, $this>
     */
    public function caseType(): BelongsTo
    {
        return $this->belongsTo(CaseType::class);
    }
}
