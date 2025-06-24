<?php

namespace AdvisingApp\Ai\Filament\Resources;

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\CreateQnaAdvisor;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\EditQnaAdvisor;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ListQnaAdvisors;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ManageCategories;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ManageQnaQuestions;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\QnaAdvisorEmbed;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ViewQnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisor;
use App\Features\QnaAdvisorFeature;
use Filament\Pages\Page;
use Filament\Resources\Resource;

class QnaAdvisorResource extends Resource
{
    protected static ?string $model = QnaAdvisor::class;

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?string $modelLabel = 'QnA Advisor';

    protected static ?int $navigationSort = 50;

    public static function canAccess(): bool
    {
        return QnaAdvisorFeature::active() && parent::canAccess();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQnaAdvisors::route('/'),
            'create' => CreateQnaAdvisor::route('/create'),
            'view' => ViewQnaAdvisor::route('/{record}'),
            'edit' => EditQnaAdvisor::route('/{record}/edit'),
            'manage-categories' => ManageCategories::route('/{record}/categories'),
            'manage-questions' => ManageQnaQuestions::route('/{record}/questions'),
            'embed' => QnaAdvisorEmbed::route('/{record}/embed'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewQnaAdvisor::class,
            EditQnaAdvisor::class,
            ManageCategories::class,
            ManageQnaQuestions::class,
            QnaAdvisorEmbed::class,
        ]);
    }
}
