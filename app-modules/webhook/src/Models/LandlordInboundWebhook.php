<?php

namespace AdvisingApp\Webhook\Models;

use AdvisingApp\Webhook\Enums\InboundWebhookSource;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class LandlordInboundWebhook extends Model
{
    use HasUuids;
    use UsesLandlordConnection;

    protected $fillable = [
        'source',
        'event',
        'url',
        'payload',
    ];

    protected $casts = [
        'source' => InboundWebhookSource::class,
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
