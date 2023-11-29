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
