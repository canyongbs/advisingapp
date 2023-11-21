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

namespace App\Filament\Pages;

use App\Models\User;
use App\Filament\Widgets\MyTasks;
use App\Filament\Widgets\MyStudents;
use App\Filament\Widgets\MyProspects;
use App\Filament\Widgets\TotalStudents;
use App\Filament\Widgets\WelcomeWidget;
use App\Filament\Widgets\TotalProspects;
use Filament\Pages\Dashboard as BasePage;
use App\Filament\Widgets\MyServiceRequests;
use App\Filament\Widgets\ProspectGrowthChart;
use App\Filament\Widgets\RecentProspectsList;
use App\Filament\Widgets\RecentKnowledgeBaseArticlesList;

class Dashboard extends BasePage
{
    protected static ?string $navigationLabel = 'My Dashboard';

    protected ?string $heading = 'My Dashboard';

    public function getWidgets(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('authorization.view_dashboard') ? [
            //1
            WelcomeWidget::class,
            //2
            TotalStudents::class,
            TotalProspects::class,
            MyStudents::class,
            MyProspects::class,
            //3
            ProspectGrowthChart::class,
            //4
            RecentProspectsList::class,
            RecentKnowledgeBaseArticlesList::class,
            //5
            MyServiceRequests::class,
            MyTasks::class,
        ] : [
            WelcomeWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 4,
        ];
    }
}
