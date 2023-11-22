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

namespace Assist\MeetingCenter\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\MeetingCenter\Enums\CalendarProvider;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperCalendar
 */
class Calendar extends BaseModel
{
    protected $hidden = [
        'oauth_token',
        'oauth_refresh_token',
        'oauth_token_expires_at',
    ];

    protected $casts = [
        'provider_id' => 'encrypted',
        'provider_type' => CalendarProvider::class,
        'provider_email' => 'encrypted',
        'oauth_token' => 'encrypted',
        'oauth_refresh_token' => 'encrypted',
        'oauth_token_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
