<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseManagement\Models;

use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\Form\Models\Submissible;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCaseForm
 */
class CaseForm extends Submissible
{
    protected $fillable = [
        'name',
        'description',
        'embed_enabled',
        'allowed_domains',
        'is_authenticated',
        'is_wizard',
        'recaptcha_enabled',
        'primary_color',
        'rounding',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
        'embed_enabled' => 'boolean',
        'allowed_domains' => 'array',
        'is_authenticated' => 'boolean',
        'is_wizard' => 'boolean',
        'recaptcha_enabled' => 'boolean',
        'rounding' => Rounding::class,
    ];

    public function getTable()
    {
        return 'case_forms';
    }

    /**
     * @return HasMany<CaseFormField, $this>
     */
    public function fields(): HasMany
    {
        return $this->hasMany(CaseFormField::class, 'case_form_id');
    }

    /**
     * @return HasMany<CaseFormStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(CaseFormStep::class, 'case_form_id');
    }

    /**
     * @return HasMany<CaseFormSubmission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(CaseFormSubmission::class, 'case_form_id');
    }

    /**
     * @return BelongsTo<CaseType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(CaseType::class, 'case_type_id');
    }
}
