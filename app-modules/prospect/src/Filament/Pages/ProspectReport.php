<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Prospect\Filament\Pages;

use Filament\Pages\Dashboard;
use App\Filament\Clusters\ReportLibrary;
use AdvisingApp\Report\Filament\Widgets\RefreshWidget;
use AdvisingApp\Prospect\Filament\Widgets\ProspectReportStats;
use AdvisingApp\Prospect\Filament\Widgets\ProspectReportLineChart;
use AdvisingApp\Prospect\Filament\Widgets\ProspectReportTableChart;

class ProspectReport extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Prospects';

    protected static string $routePath = 'prospect-report';

    protected static ?string $title = 'Prospects (Overview)';

    protected static ?string $cluster = ReportLibrary::class;

    protected $cacheTag = 'prospect-report-cache';

    public function getWidgets(): array
    {
        return [
            RefreshWidget::make(['cacheTag' => $this->cacheTag]),
            ProspectReportStats::make(['cacheTag' => $this->cacheTag]),
            ProspectReportLineChart::make(['cacheTag' => $this->cacheTag]),
            ProspectReportTableChart::make(['cacheTag' => $this->cacheTag]),
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
        ];
    }
}
