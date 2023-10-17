<?php

namespace Assist\Campaign\Models;

use App\Models\BaseModel;
use Filament\Forms\Components\TextInput;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\Campaign\Enums\CampaignActionType;
use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

class CampaignAction extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'data',
    ];

    protected $casts = [
        'type' => CampaignActionType::class,
        'data' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    // TODO After successful execution, we need to update the executed_at
    public function execute(): void
    {
        ray('execute()', $this->type);
        match ($this->type) {
            CampaignActionType::BulkEngagement => EngagementBatch::executeFromCampaignAction($this),
            default => null
        };
    }

    public function hasBeenExecuted(): bool
    {
        return (bool) ! is_null($this->executed_at);
    }

    // TODO Make this dynamic based on the type
    public function getEditFields(): array
    {
        return [
            TextInput::make('data.subject')
                ->required()
                ->maxLength(255),
            TextInput::make('data.body')
                ->required()
                ->maxLength(255),
            TextInput::make('data.delivery_methods')
                ->required()
                ->maxLength(255),
        ];
    }
}
