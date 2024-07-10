<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use Filament\Tables\Table;
use Livewire\Attributes\On;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use AdvisingApp\StudentDataModel\Models\Student;

class MostEngagedStudentsTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Most Actively Engaged Students';

    protected int | string | array $columnSpan = 'full';

    public function mount(string $cacheTag)
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::select('sisid', 'full_name', 'email')
                    ->withCount('engagements')
            )
            ->defaultSort('engagements_count', 'desc')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('engagements_count')
                    ->label('Engagements'),
            ]);
    }
}
