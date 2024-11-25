<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources;

use App\Models\Tag;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\ConstituentManagement;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource\Pages\EditStudentTag;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource\Pages\ViewStudentTag;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource\Pages\ListStudentTags;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentTagResource\Pages\CreateStudentTag;

class StudentTagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Tags';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    public static function getPages(): array
    {
        return [
            'index' => ListStudentTags::route('/'),
            'create' => CreateStudentTag::route('/create'),
            'view' => ViewStudentTag::route('/{record}'),
            'edit' => EditStudentTag::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', 'Student');
    }
}
