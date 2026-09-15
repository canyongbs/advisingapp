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

use AdvisingApp\Application\Actions\DeliverApplicationSubmissionRequestByEmail;
use AdvisingApp\Application\Actions\DeliverApplicationSubmissionRequestBySms;
use AdvisingApp\Application\Models\Concerns\HasRelationBasedStateMachine;
use AdvisingApp\Application\Observers\ApplicationSubmissionObserver;
use AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;
use AdvisingApp\Form\Models\Submission;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;
use App\Models\User;
use CanyonGBS\Common\Models\Concerns\CanBeArchived;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperApplicationSubmission
 */
#[ObservedBy([ApplicationSubmissionObserver::class])]
class ApplicationSubmission extends Submission
{
    use CanBeArchived;
    use HasRelationBasedStateMachine;

    protected $fillable = [
        'canceled_at',
        'application_id',
        'request_method',
        'request_note',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'immutable_datetime',
        'canceled_at' => 'immutable_datetime',
        'request_method' => FormSubmissionRequestDeliveryMethod::class,
    ];

    /**
     * @return BelongsTo<Application, $this>
     */
    public function submissible(): BelongsTo
    {
        return $this
            ->belongsTo(Application::class, 'application_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * @return BelongsToMany<ApplicationField, $this, covariant ApplicationFieldSubmission>
     */
    public function fields(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                ApplicationField::class,
                'application_field_submission',
                'submission_id',
                'field_id'
            )
            ->withPivot(['id', 'response'])
            ->using(ApplicationFieldSubmission::class);
    }

    /**
     * @return BelongsTo<ApplicationSubmissionState, $this>
     */
    public function state(): BelongsTo
    {
        return $this
            ->belongsTo(ApplicationSubmissionState::class, 'state_id');
    }

    public function getStateMachineFields(): array
    {
        return [
            'state.classification',
        ];
    }

    /**
     * @return HasMany<ApplicationSubmissionsChecklistItem, $this>
     */
    public function checklistItems(): HasMany
    {
        return $this->hasMany(ApplicationSubmissionsChecklistItem::class, 'application_submission_id');
    }

    public function deliverRequest(): void
    {
        match ($this->request_method) {
            FormSubmissionRequestDeliveryMethod::Email => DeliverApplicationSubmissionRequestByEmail::dispatch($this),
            FormSubmissionRequestDeliveryMethod::Sms => DeliverApplicationSubmissionRequestBySms::dispatch($this),
            default => null,
        };
    }

    /**
     * @param Builder<ApplicationSubmission> $query
     *
     * @return Builder<ApplicationSubmission>
     */
    public function scopeRequested(Builder $query): Builder
    {
        return $query->notSubmitted()->notCanceled();
    }

    /**
     * @param Builder<ApplicationSubmission> $query
     *
     * @return Builder<ApplicationSubmission>
     */
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereNotNull('submitted_at');
    }

    /**
     * @param Builder<ApplicationSubmission> $query
     *
     * @return Builder<ApplicationSubmission>
     */
    public function scopeCanceled(Builder $query): Builder
    {
        return $query->notSubmitted()->whereNotNull('canceled_at');
    }

    /**
     * @param Builder<ApplicationSubmission> $query
     *
     * @return Builder<ApplicationSubmission>
     */
    public function scopeNotSubmitted(Builder $query): Builder
    {
        return $query->whereNull('submitted_at');
    }

    /**
     * @param Builder<ApplicationSubmission> $query
     *
     * @return Builder<ApplicationSubmission>
     */
    public function scopeNotCanceled(Builder $query): Builder
    {
        return $query->whereNull('canceled_at');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('author'));
        });
    }
}
