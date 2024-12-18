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

use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'programs';

    public function infolist(Infolist $infolist): Infolist
    {
        $sisSystem = app(StudentInformationSystemSettings::class)->sis_system;

        return $infolist
            ->schema([
                TextEntry::make('sisid')
                    ->label('SISID'),
                TextEntry::make('otherid')
                    ->label('STUID')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsOtherid() ?? true),
                TextEntry::make('division')
                    ->label('College')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsDivision() ?? true),
                TextEntry::make('descr')
                    ->label('Program')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsDescr() ?? true),
                TextEntry::make('acad_plan')
                    ->label('Program details')
                    ->placeholder('-')
                    ->state(function (Program $record): ?string {
                        $acadPlan = $record->acad_plan;

                        if (blank($acadPlan)) {
                            return null;
                        }

                        if (! is_array($acadPlan)) {
                            return null;
                        }

                        $majors = $acadPlan['major'] ?? [];
                        $minors = $acadPlan['minor'] ?? [];

                        if (blank($majors) && blank($minors)) {
                            return null;
                        }

                        $state = [];

                        if (filled($majors)) {
                            $state[] = 'Major: ' . implode(', ', $majors);
                        }

                        if (filled($minors)) {
                            $state[] = 'Minor: ' . implode(', ', $minors);
                        }

                        return implode('; ', $state);
                    })
                    ->visible($sisSystem?->hasProgramsAcadPlan() ?? true)
                    ->columnSpanFull(),
                TextEntry::make('foi')
                    ->label('Field of Interest')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsFoi() ?? true),
                TextEntry::make('cum_gpa')
                    ->label('Cumulative GPA')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsCumGpa() ?? true),
                TextEntry::make('declare_dt')
                    ->label('Start Date')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsDeclareDt() ?? true),
                TextEntry::make('change_dt')
                    ->label('Last Action Date')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsChangeDt() ?? true),
                TextEntry::make('graduation_dt')
                    ->label('Graduation Date')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsGraduationDt() ?? true),
                TextEntry::make('conferred_dt')
                    ->label('Conferred Date')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsConferredDt() ?? true),
            ]);
    }

    public function table(Table $table): Table
    {
        $sisSystem = app(StudentInformationSystemSettings::class)->sis_system;

        return $table
            ->recordTitleAttribute('descr')
            ->columns([
                TextColumn::make('otherid')
                    ->label('STUID')
                    ->visible($sisSystem?->hasProgramsOtherid() ?? true),
                TextColumn::make('division')
                    ->label('College')
                    ->visible($sisSystem?->hasProgramsDivision() ?? true),
                TextColumn::make('descr')
                    ->label('Program')
                    ->visible($sisSystem?->hasProgramsDescr() ?? true),
                TextColumn::make('foi')
                    ->label('Field of Interest')
                    ->visible($sisSystem?->hasProgramsFoi() ?? true),
                TextColumn::make('cum_gpa')
                    ->label('Cumulative GPA')
                    ->visible($sisSystem?->hasProgramsCumGpa() ?? true),
                TextColumn::make('declare_dt')
                    ->label('Start Date')
                    ->visible($sisSystem?->hasProgramsDeclareDt() ?? true),
                TextColumn::make('graduation_dt')
                    ->label('Graduation Date')
                    ->visible($sisSystem?->hasProgramsGraduationDt() ?? true),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }
}
