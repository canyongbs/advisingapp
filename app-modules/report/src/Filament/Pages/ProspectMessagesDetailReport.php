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

namespace AdvisingApp\Report\Filament\Pages;

use AdvisingApp\Report\Abstract\ProspectReport;
use AdvisingApp\Report\Filament\Widgets\ProspectMessagesDetailStats;
use AdvisingApp\Report\Filament\Widgets\ProspectMessagesDetailTable;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use App\Features\HolisticEngagementFeature;
use App\Filament\Clusters\ReportLibrary;

class ProspectMessagesDetailReport extends ProspectReport
{
    protected static ?string $title = 'Messages Detail';

    protected static ?string $cluster = ReportLibrary::class;

    protected static string $routePath = 'prospect-messages-detail-report';

    protected static ?string $navigationGroup = 'Prospects';

    protected string $cacheTag = 'report-prospect-messages-detail';

    protected static ?int $navigationSort = 50;

    public static function canAccess(): bool
    {
        return HolisticEngagementFeature::active() && parent::canAccess();
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
        ];
    }

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            ProspectMessagesDetailStats::make(['cacheTag' => $this->cacheTag]),
            ProspectMessagesDetailTable::make(['cacheTag' => $this->cacheTag]),
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'filters' => $this->filters,
        ];
    }
}
