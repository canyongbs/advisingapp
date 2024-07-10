<?php

namespace AdvisingApp\Prospect\Filament\Widgets;

use Filament\Tables\Table;
use Livewire\Attributes\On;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Widgets\TableWidget as BaseWidget;

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
                Prospect::select('id', 'full_name', 'email', 'status_id', 'created_by_id', 'created_at')
                    ->with(['status', 'createdBy:id,name'])
                    ->withCount('engagements')
                    ->orderBy('engagements_count', 'desc')
                    ->limit(10)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name'),
                TextColumn::make('email'),
                TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (Prospect $record) => $record->status->color->value),
                TextColumn::make('engagements_count')
                    ->label('Engagements'),
                TextColumn::make('createdBy.name')
                    ->label('Created By'),
                TextColumn::make('created_at')
                    ->label('Created Date'),
            ]);
    }
}
