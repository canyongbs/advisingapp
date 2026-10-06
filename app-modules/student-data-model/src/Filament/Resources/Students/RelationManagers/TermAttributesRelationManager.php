<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\Term;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TermAttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'termAttributes';

    protected static ?string $title = 'Term Attributes';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return app(StudentInformationSystemSettings::class)->hasTermAttributes()
            && parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sis_term_id')
            ->columns([
                TextColumn::make('enrollment_status')
                    ->label('Enrollment Status')
                    ->placeholder('N/A'),
                TextColumn::make('academic_status')
                    ->label('Academic Status')
                    ->placeholder('N/A'),
                TextColumn::make('campus')
                    ->label('Campus')
                    ->placeholder('N/A'),
                TextColumn::make('college_level')
                    ->label('College Level')
                    ->placeholder('N/A'),
                TextColumn::make('commuter')
                    ->label('Commuter')
                    ->placeholder('N/A'),
                TextColumn::make('student_registered')
                    ->label('Student Registered')
                    ->placeholder('N/A'),
            ])
            ->filters([
                SelectFilter::make('sis_term_id')
                    ->label('Term')
                    ->options(fn (): array => $this->getTermOptions())
                    ->default(function (): ?string {
                        $latestTermId = array_key_first($this->getTermOptions());

                        return $latestTermId === null ? null : (string) $latestTermId;
                    })
                    ->selectablePlaceholder(false)
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->deferFilters(false)
            ->paginated(false)
            ->emptyStateHeading('No term attributes');
    }

    /**
     * The terms this student has attribute records for, latest first.
     * Keys are `sis_term_id` values; PHP casts numeric string keys (e.g. `'267'`) to integers.
     *
     * @return array<int|string, string>
     */
    protected function getTermOptions(): array
    {
        $student = $this->getOwnerRecord();

        assert($student instanceof Student);

        return Term::query()
            ->whereIn('sis_term_id', $student->termAttributes()->select('sis_term_id'))
            ->orderByRaw('start_date DESC NULLS LAST')
            ->get(['sis_term_id', 'name', 'start_date'])
            ->mapWithKeys(fn (Term $term): array => [$term->sis_term_id => $term->getDisplayName()])
            ->all();
    }
}
