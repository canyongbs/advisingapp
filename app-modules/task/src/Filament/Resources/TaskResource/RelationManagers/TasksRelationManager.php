<?php

namespace Assist\Task\Filament\Resources\TaskResource\RelationManagers;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Assist\Task\Models\Task;
use Assist\Task\Enums\TaskStatus;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Assist\AssistDataModel\Models\Student;
use Filament\Forms\Components\DateTimePicker;
use Assist\Prospect\Filament\Resources\ProspectResource;
use App\Filament\Resources\RelationManagers\RelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Task\Filament\Resources\TaskResource\Components\TaskViewAction;

class TasksRelationManager extends RelationManager
{

}
