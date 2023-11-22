<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class NotificationSettingPivot extends MorphPivot
{
    use HasUuids;

    public $timestamps = true;

    protected $table = 'notification_settings_pivot';

    protected $fillable = [
        'notification_setting_id',
        'related_to_id',
        'related_to_type',
    ];

    public function setting(): BelongsTo
    {
        return $this->belongsTo(NotificationSetting::class, 'notification_setting_id');
    }

    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }
}
