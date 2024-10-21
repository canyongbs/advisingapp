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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                    TextEntry::make('sisid')
                        ->label('SISID'),
                    TextEntry::make('division')
                        ->label('College'),
                    TextEntry::make('class_nbr')
                        ->label('Course'),
                    TextEntry::make('crse_grade_off')
                        ->label('Grade'),
                    TextEntry::make('unt_taken')
                        ->label('Attempted'),
                    TextEntry::make('unt_earned')
                        ->label('Earned'),
                    TextEntry::make('section')
                        ->label('Section')
                        ->default('N/A'),
                    TextEntry::make('name')
                        ->label('Name')
                        ->default('N/A'),
                    TextEntry::make('department')
                        ->label('Department')
                        ->default('N/A'),
                    TextEntry::make('faculty_name')
                        ->label('Faculty Name')
                        ->default('N/A'),
                    TextEntry::make('faculty_email')
                        ->label('Faculty Email')
                        ->default('N/A'),
                    TextEntry::make('semester_code')
                        ->label('Semester Code')
                        ->default('N/A'),
                    TextEntry::make('semester_name')
                        ->label('Semester Name')
                        ->default('N/A'),
                    TextEntry::make('start_date')
                        ->label('Start Date')
                        ->default('N/A'),
                    TextEntry::make('end_date')
                        ->label('End Date')
                        ->default('N/A'),
                ]
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('division')
            ->columns([
                TextColumn::make('division')
                    ->label('College'),
                TextColumn::make('class_nbr')
                    ->label('Course'),
                TextColumn::make('crse_grade_off')
                    ->label('Grade'),
                TextColumn::make('unt_taken')
                    ->label('Attempted'),
                TextColumn::make('unt_earned')
                    ->label('Earned'),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }
}
