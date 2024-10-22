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

namespace AdvisingApp\StudentDataModel\Filament\Resources\ManageProgramResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\StudentDataModel\Models\Program;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use AdvisingApp\StudentDataModel\Filament\Imports\ProgramImporter;
use AdvisingApp\StudentDataModel\Filament\Resources\ManageProgramResource;

class ListManagePrograms extends ListRecords
{
    protected static string $resource = ManageProgramResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('otherid')
                    ->label('STUID'),
                TextColumn::make('division')
                    ->label('College'),
                TextColumn::make('descr')
                    ->label('Program'),
                TextColumn::make('foi')
                    ->label('Field of Interest'),
                TextColumn::make('cum_gpa')
                    ->label('Cumulative GPA'),
                TextColumn::make('declare_dt')
                    ->label('Start Date'),
            ])->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed'),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(ProgramImporter::class)
                ->authorize('import', Program::class),
        ];
    }
}
