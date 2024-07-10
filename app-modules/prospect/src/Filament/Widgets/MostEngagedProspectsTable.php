<?php

namespace AdvisingApp\Prospect\Filament\Widgets;

use AdvisingApp\Prospect\Models\Prospect;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class MostEngagedProspectsTable extends BaseWidget
{
    public string $cacheTag;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Most Actively Engaged Prospects';

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

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
                Prospect::select('id', 'full_name', 'email', 'status_id', 'created_by_id','created_at')
                            ->with(['status', 'createdBy:id,name'])
                            ->withCount('engagements')
            )
            ->defaultSort('engagements_count','desc')
            ->columns([
                TextColumn::make('full_name')
                            ->label('Name')
                            ->searchable(),
                TextColumn::make('email')
                            ->searchable(),
                TextColumn::make('status.name')
                            ->badge()
                            ->translateLabel()
                            ->color(fn (Prospect $record) => $record->status->color->value),
                TextColumn::make('engagements_count')
                            ->label('Engagements'),
                TextColumn::make('createdBy.name')
                            ->label('Created By')
                            ->searchable(),
                TextColumn::make('created_at')
                            ->label('Created Date'),
            ]);
    }
}
