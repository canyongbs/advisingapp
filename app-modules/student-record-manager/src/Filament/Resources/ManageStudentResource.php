<?php

namespace AdvisingApp\StudentRecordManager\Filament\Resources;

use AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource\Pages;
use AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource\RelationManagers;
use AdvisingApp\StudentRecordManager\Models\ManageableStudent;
use App\Filament\Clusters\ConstituentManagement;
use App\Models\ManageStudent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageStudentResource extends Resource
{
    protected static ?string $model = ManageableStudent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Students';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManageStudents::route('/'),
            'create' => Pages\CreateManageStudent::route('/create'),
            'view' => Pages\ViewManageStudent::route('/{record}'),
            'edit' => Pages\EditManageStudent::route('/{record}/edit'),
        ];
    }
}
