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

namespace AdvisingApp\Report\Filament\Resources\ReportResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Report\Filament\Resources\ReportResource;

class ListReports extends ListRecords
{
    protected ?string $heading = 'Custom Reports';

    protected static string $resource = ReportResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('model')
                    ->sortable()
                    ->visible(auth()->user()->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()]) || auth()->user()->can('viewAny', User::class)),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->sortable()
                    ->hidden(function (Table $table) {
                        return $table->getFilter('my_reports')->getState()['isActive'];
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([
                Filter::make('my_reports')
                    ->label('My Custom Reports')
                    ->query(
                        fn ($query) => $query->where('user_id', auth()->id())
                    )
                    ->default(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
