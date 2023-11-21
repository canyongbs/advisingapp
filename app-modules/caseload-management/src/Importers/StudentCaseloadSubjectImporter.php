<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\CaseloadManagement\Importers;

use App\Models\Import;
use App\Imports\Importer;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Assist\AssistDataModel\Models\Student;
use App\Filament\Actions\ImportAction\ImportColumn;
use Assist\CaseloadManagement\Models\CaseloadSubject;

class StudentCaseloadSubjectImporter extends Importer
{
    protected static ?string $model = CaseloadSubject::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('subject')
                ->label('Student ID / Other ID')
                ->rules(['required'])
                ->relationship(
                    resolveUsing: fn (mixed $state) => Student::query()
                        ->where('sisid', $state)
                        ->orWhere('otherid', $state)
                        ->first(),
                )
                ->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?Model
    {
        return new CaseloadSubject();
    }

    public function beforeCreate(): void
    {
        $this->record->caseload()->associate($this->getOptions()['caseload_id']);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your caseload import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('student', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('student', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
