<?php

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
