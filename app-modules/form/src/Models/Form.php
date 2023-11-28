<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\Form\Models;

use Assist\Form\Enums\Rounding;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperForm
 */
class Form extends Submissible
{
    protected $fillable = [
        'name',
        'description',
        'embed_enabled',
        'allowed_domains',
        'is_authenticated',
        'is_wizard',
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
        'rounding' => Rounding::class,
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(FormStep::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    protected function isWizard(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool) $value,
        );
    }

    protected function content(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
        );
    }
}
