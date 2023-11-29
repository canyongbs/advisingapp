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

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * @property string $name
 * @property ?array $content
 * @property bool $embed_enabled
 * @property bool $is_wizard
 * @property ?array $allowed_domains
 * @property-read Collection<int, SubmissibleStep> $steps
 * @property-read Collection<int, SubmissibleField> $fields
 */
abstract class Submissible extends Model
{
    use HasFactory;
    use DefinesPermissions;
    use HasUuids;

    abstract public function fields(): HasMany;

    abstract public function steps(): HasMany;

    abstract public function submissions(): HasMany;

    protected function name(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('name') ? $this->castAttribute('name', $value) : $value);
    }

    protected function content(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('content') ? $this->castAttribute('content', $value) : $value);
    }

    protected function embedEnabled(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('embed_enabled') ? $this->castAttribute('embed_enabled', $value) : $value);
    }

    protected function allowedDomains(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('allowed_domains') ? $this->castAttribute('allowed_domains', $value) : $value);
    }

    protected function isWizard(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('is_wizard') ? $this->castAttribute('is_wizard', $value) : $value);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
