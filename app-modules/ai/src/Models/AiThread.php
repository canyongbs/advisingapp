<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Models;

use AdvisingApp\Ai\Events\AiThreadForceDeleting;
use AdvisingApp\Ai\Events\AiThreadTrashed;
use AdvisingApp\Ai\Models\Concerns\CanAddAssistantLicenseGlobalScope;
use App\Models\BaseModel;
use App\Models\User;
use App\Settings\DisplaySettings;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Livewire\Wireable;

/**
 * @mixin IdeHelperAiThread
 */
class AiThread extends BaseModel implements Wireable
{
    use CanAddAssistantLicenseGlobalScope;
    use SoftDeletes;
    use Prunable;

    protected $fillable = [
        'thread_id',
        'name',
        'assistant_id',
        'folder_id',
        'user_id',
        'locked_at',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'saved_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'trashed' => AiThreadTrashed::class,
        'forceDeleting' => AiThreadForceDeleting::class,
    ];

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'assistant_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(AiThreadFolder::class, 'folder_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'thread_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            table: 'ai_messages',
            foreignPivotKey: 'thread_id',
        )->using(AiMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prunable(): Builder
    {
        return static::query()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', now()->subDays(7))
            ->whereDoesntHave('messages', fn (Builder $query) => $query->withTrashed());
    }

    public function toLivewire()
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
        ];
    }

    public static function fromLivewire($value)
    {
        return AiThread::query()->find($value['id']);
    }

    protected function lastEngagedAt(): Attribute
    {
        return Attribute::make(
            get: function (): ?CarbonInterface {
                $timezone = app(DisplaySettings::class)->getTimezone();

                $date = array_key_exists('messages_max_created_at', $this->getAttributes())
                    ? $this->getAttributeValue('messages_max_created_at')
                    : $this->messages()
                        ->latest()
                        ->value('created_at');

                if (! $date) {
                    return null;
                }

                return Carbon::parse($date)->setTimezone($timezone);
            }
        );
    }
}
