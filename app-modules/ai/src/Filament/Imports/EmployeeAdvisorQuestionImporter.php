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

namespace AdvisingApp\Ai\Filament\Imports;

use AdvisingApp\Ai\Models\EmployeeAdvisorCategory;
use AdvisingApp\Ai\Models\EmployeeAdvisorQuestion;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;
use InvalidArgumentException;

class EmployeeAdvisorQuestionImporter extends Importer
{
    public const IMPORT_NAME = 'Employee Chatbot Questions';

    protected static ?string $model = EmployeeAdvisorQuestion::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('question')
                ->rules(['required', 'string', 'max:255'])
                ->requiredMapping()
                ->example('What is the password reset process?'),
            ImportColumn::make('answer')
                ->rules(['required', 'string', 'max:65535'])
                ->requiredMapping()
                ->example('Go to login and click forgot password.'),
            ImportColumn::make('category')
                ->label('Category')
                ->rules(['required', 'string', 'max:255'])
                ->requiredMapping()
                ->example('Knowledge Base'),
        ];
    }

    public function resolveRecord(): ?EmployeeAdvisorQuestion
    {
        $categoryName = $this->data['category'] ?? null;
        $advisorId = $this->getEmployeeAdvisorId();

        if (blank($categoryName)) {
            throw new RowImportFailedException('The category field is required.');
        }

        $category = EmployeeAdvisorCategory::where('employee_advisor_id', $advisorId)
            ->whereRaw('LOWER(name) = ?', [Str::lower($categoryName)])
            ->first();

        if (! $category) {
            throw new RowImportFailedException("The category '{$categoryName}' is invalid.");
        }

        $question = new EmployeeAdvisorQuestion();
        $question->category_id = $category->getKey();

        return $question;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your ' . static::IMPORT_NAME . ' import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function getEmployeeAdvisorId(): string
    {
        $id = $this->options['employee_advisor_id'] ?? null;

        if (blank($id)) {
            throw new InvalidArgumentException('The employee_advisor_id option is required for this import.');
        }

        return (string) $id;
    }
}
