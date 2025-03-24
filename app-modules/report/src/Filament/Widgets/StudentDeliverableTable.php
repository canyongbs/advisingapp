<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Livewire\Attributes\On;

class StudentDeliverableTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Student Communication Preferences';

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
                Student::select('sisid', 'full_name', 'email_bounce', 'sms_opt_out')
                    ->where('sms_opt_out', true)
                    ->orWhere('email_bounce', true)
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name'),
                IconColumn::make('email_bounce')
                    ->label('Email Opt Out')
                    ->boolean(),
                IconColumn::make('sms_opt_out')
                    ->label('SMS Opt Out')
                    ->boolean(),
            ]);
    }
}
