<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property bool $is_required
 * @property string $type
 * @property string $label
 * @property array $config
 * @property-read Submissible $submissible
 * @property-read SubmissibleStep $step
 */
abstract class SubmissibleField extends BaseModel
{
    abstract public function submissible(): BelongsTo;

    abstract public function step(): BelongsTo;

    protected function isRequired(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('is_required') ? $this->castAttribute('is_required', $value) : $value);
    }

    protected function type(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('type') ? $this->castAttribute('type', $value) : $value);
    }

    protected function label(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('label') ? $this->castAttribute('label', $value) : $value);
    }

    protected function config(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('config') ? $this->castAttribute('config', $value) : $value);
    }
}
