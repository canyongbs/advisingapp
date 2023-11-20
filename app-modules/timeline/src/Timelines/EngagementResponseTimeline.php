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

namespace Assist\Timeline\Timelines;

use Filament\Actions\ViewAction;
use Assist\Timeline\Models\CustomTimeline;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Filament\Resources\EngagementResponseResource\Components\EngagementResponseViewAction;

// TODO Decide where these belong - might want to keep these in the context of the original module
class EngagementResponseTimeline extends CustomTimeline
{
    public function __construct(
        public EngagementResponse $engagementResponse
    ) {}

    public function icon(): string
    {
        return 'heroicon-o-arrow-small-left';
    }

    public function sortableBy(): string
    {
        return $this->engagementResponse->sent_at;
    }

    public function providesCustomView(): bool
    {
        return true;
    }

    public function renderCustomView(): string
    {
        return 'engagement::engagement-response-timeline-item';
    }

    public function modalViewAction(): ViewAction
    {
        return EngagementResponseViewAction::make()->record($this->engagementResponse);
    }
}
