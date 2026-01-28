<?php

namespace App\Livewire;

use App\Models\Export;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Component;

class ExportHubTable extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(Export::query())
            ->columns([
                TextColumn::make('requestor')
                    ->getStateUsing(function (Export $record): string {
                        /** @var User $user */
                        $user = $record->user;

                        return $user->name;
                    }),
                TextColumn::make('exporter')
                    ->label('Export Name')
                    ->getStateUsing(fn (Export $record): string => Str::of(class_basename($record->exporter))
                        ->replaceLast('Exporter', '')
                        ->headline() . ' Export'),
                TextColumn::make('created_at')
                    ->label('Date Started')
                    ->dateTime(),
                TextColumn::make('completed_at')
                    ->label('Date Completed')
                    ->dateTime(),
            ])->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Export $record) => URL::signedRoute('exports.download', $record))
                    ->visible(fn (Export $record) => $record->completed_at !== null && auth()->user()->can('export_hub.import')),
            ]);
    }

    public function render(): View
    {
        return view('livewire.export-hub-table');
    }
}
