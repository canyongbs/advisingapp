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

namespace AdvisingApp\Ai\Filament\Resources;

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\CreateQnAAdvisor;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\EditQnAAdvisor;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ListQnAAdvisors;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ManageCategories;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ManageQnAQuestions;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\QnAAdvisorEmbed;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ViewQnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisor;
use App\Features\QnAAdvisorFeature;
use Filament\Pages\Page;
use Filament\Resources\Resource;

class QnAAdvisorResource extends Resource
{
    protected static ?string $model = QnAAdvisor::class;

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?string $modelLabel = 'QnA Advisor';

    protected static ?int $navigationSort = 50;

    public static function canAccess(): bool
    {
        return QnAAdvisorFeature::active() && parent::canAccess();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQnAAdvisors::route('/'),
            'create' => CreateQnAAdvisor::route('/create'),
            'view' => ViewQnAAdvisor::route('/{record}'),
            'edit' => EditQnAAdvisor::route('/{record}/edit'),
            'manage-categories' => ManageCategories::route('/{record}/categories'),
            'manage-questions' => ManageQnAQuestions::route('/{record}/questions'),
            'embed' => QnAAdvisorEmbed::route('/{record}/embed'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewQnAAdvisor::class,
            EditQnAAdvisor::class,
            ManageCategories::class,
            ManageQnAQuestions::class,
            QnAAdvisorEmbed::class,
        ]);
    }
}
