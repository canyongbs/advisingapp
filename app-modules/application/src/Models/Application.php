<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Application\Models;

use AdvisingApp\Application\Observers\ApplicationObserver;
use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Enums\FontWeight;
use App\Models\Media;
use App\Models\User;
use CanyonGBS\Common\Models\Concerns\CanBeArchived;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @mixin IdeHelperApplication
 */
#[ObservedBy([ApplicationObserver::class])]
class Application extends Submissible implements HasMedia, HasRichContent
{
    use CanBeArchived;
    use HasRelationships;

    /** @use InteractsWithMedia<Media> */
    use InteractsWithMedia;

    use InteractsWithRichContent;

    protected ?bool $hasSubmissions = null;

    protected $fillable = [
        'name',
        'description',
        'embed_enabled',
        'allowed_domains',
        'is_wizard',
        'primary_color',
        'rounding',
        'content',
        'should_generate_prospects',
        'title',
        'title_color',
        'title_font_weight',
        'notify_to_care_team',
        'notify_to_subscribers',
        'notify_via_app',
        'notify_via_email',
        'root_id',
        'allow_view_past_submissions',
    ];

    protected $casts = [
        'content' => 'array',
        'embed_enabled' => 'boolean',
        'allowed_domains' => 'array',
        'is_wizard' => 'boolean',
        'rounding' => Rounding::class,
        'should_generate_prospects' => 'boolean',
        'title_font_weight' => FontWeight::class,
        'notify_to_care_team' => 'boolean',
        'notify_to_subscribers' => 'boolean',
        'notify_via_app' => 'boolean',
        'notify_via_email' => 'boolean',
        'allow_view_past_submissions' => 'boolean',
    ];

    public function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->fileAttachmentsDisk('s3-public')
            ->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make());
    }

    /**
     * @return HasMany<ApplicationField, $this>
     */
    public function fields(): HasMany
    {
        return $this->hasMany(ApplicationField::class);
    }

    /**
     * @return HasMany<ApplicationStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ApplicationStep::class);
    }

    /**
     * @return HasMany<ApplicationSubmission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(ApplicationSubmission::class);
    }

    /**
     * @return MorphMany<WorkflowTrigger, $this>
     */
    public function workflowTriggers(): MorphMany
    {
        return $this->morphMany(WorkflowTrigger::class, 'related');
    }

    /**
     * @return HasManyDeep<Model, $this>
     */
    public function workflows(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->workflowTriggers(), (new WorkflowTrigger())->workflow());
    }

    /**
     * @return BelongsToMany<User, $this, ApplicationNotificationUser>
     */
    public function notificationUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'application_notification_users', 'application_id', 'user_id')
            ->using(ApplicationNotificationUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsTo<Application, $this>
     */
    public function rootApplication(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'root_id');
    }

    /**
     * @return HasMany<Application, $this>
     */
    public function versions(): HasMany
    {
        return $this->hasMany(Application::class, 'root_id', 'root_id');
    }

    public function latestVersion(): ?Application
    {
        return Application::query()->where('root_id', $this->root_id)
            ->whereNull('archived_at')
            ->first();
    }

    /**
     * @param Builder<Application> $query
     */
    public function used(Builder $query): void
    {
        $query->whereHas(
            'versions',
            fn (Builder $query) => $query
                ->withoutGlobalScopes()
                ->whereHas('submissions'),
        );
    }

    public function isUsed(): bool
    {
        return (bool) ($this->hasSubmissions ??= ApplicationSubmission::query()
            ->whereHas(
                'submissible',
                fn (Builder $query) => $query->withoutGlobalScopes()->where('root_id', $this->root_id),
            )
            ->exists());
    }
}
