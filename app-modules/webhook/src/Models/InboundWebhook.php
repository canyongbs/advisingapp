<?php

namespace Assist\Webhook\Models;

use App\Models\BaseModel;
use Assist\Webhook\Enums\InboundWebhookSource;

/**
 * Assist\Webhook\Models\InboundWebhook
 *
 * @property string $id
 * @property InboundWebhookSource $source
 * @property string $event
 * @property string $url
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereUrl($value)
 *
 * @mixin \Eloquent
 * @mixin IdeHelperInboundWebhook
 */
class InboundWebhook extends BaseModel
{
    protected $fillable = [
        'source',
        'event',
        'url',
        'payload',
    ];

    protected $casts = [
        'source' => InboundWebhookSource::class,
    ];
}
