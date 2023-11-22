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

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\LaravelSettings\Models\SettingsProperty as BaseSettingsProperty;

/**
 * @mixin IdeHelperSettingsProperty
 */
class SettingsProperty extends BaseSettingsProperty implements HasMedia
{
    use HasUuids;
    use InteractsWithMedia;

    public static function getInstance(string $property): ?static
    {
        [$group, $name] = explode('.', $property);

        return static::query()
            ->where('group', $group)
            ->where('name', $name)
            ->first();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('logo-height-250px')
            ->performOnCollections('logo', 'dark_logo')
            ->height(250)
            ->keepOriginalImageFormat();
    }
}
