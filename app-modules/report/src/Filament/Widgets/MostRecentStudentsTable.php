<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Filament\Tables\Table;
use Laravel\Pennant\Feature;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use AdvisingApp\StudentDataModel\Models\Student;

class MostRecentStudentsTable extends BaseWidget
{
    protected static ?string $heading = 'Most Recent Students Added';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function () {
                    if (Feature::active('student_timestamp_fields')) {
                        $key = (new Student())->getKeyName();

                        return Student::whereIn($key, function ($query) use ($key) {
                            $query->select($key)
                                ->from((new Student())->getTable())
                                ->orderBy('created_at_source', 'desc')
                                ->take(100);
                        })->orderBy('created_at_source', 'desc');
                    } else {
                        return Student::query()->whereRaw('1 = 0');
                    }
                }
            )
            ->columns([
                TextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('sisid')
                    ->searchable(),
                TextColumn::make('otherid')
                    ->searchable(),
                TextColumn::make('created_at_source')
                    ->label('Created'),
            ])
            ->paginated([10]);
    }
}
