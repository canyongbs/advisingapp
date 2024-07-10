<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Report\Filament\Widgets;

use Filament\Tables\Table;
use Livewire\Attributes\On;
use Laravel\Pennant\Feature;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use AdvisingApp\StudentDataModel\Models\Student;

class MostRecentStudentsTable extends BaseWidget
{
    public string $cacheTag;

    protected static ?string $heading = 'Most Recent Students Added';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 4,
        'lg' => 4,
    ];

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
                    ->label('Name'),
                TextColumn::make('email'),
                TextColumn::make('sisid'),
                TextColumn::make('otherid'),
                TextColumn::make('created_at_source')
                    ->label('Created'),
            ])
            ->paginated([10]);
    }
}
