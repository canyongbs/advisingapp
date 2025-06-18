<?php

namespace AdvisingApp\Ai\Filament\Resources;

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\CreateQnAAdvisor;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\EditQnAAdvisor;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ListQnAAdvisors;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ManageCategories;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ManageQnAQuestions;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ViewQnAAdvisor;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\RelationManagers;
use AdvisingApp\Ai\Models\QnAAdvisor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QnAAdvisorResource extends Resource
{
    protected static ?string $model = QnAAdvisor::class;

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?string $modelLabel = 'QnA Advisor';

    protected static ?int $navigationSort = 50;

    public static function getPages(): array
    {
        return [
            'index' => ListQnAAdvisors::route('/'),
            'create' => CreateQnAAdvisor::route('/create'),
            'view' => ViewQnAAdvisor::route('/{record}'),
            'edit' => EditQnAAdvisor::route('/{record}/edit'),
            'manage-categories' => ManageCategories::route('/{record}/categories'),
            'manage-questions' => ManageQnAQuestions::route('/{record}/questions'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewQnAAdvisor::class,
            EditQnAAdvisor::class,
            ManageCategories::class,
            ManageQnAQuestions::class
        ]);
    }
}
