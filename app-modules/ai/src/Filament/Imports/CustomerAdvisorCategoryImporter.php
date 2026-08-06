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

use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class CustomerAdvisorCategoryImporter extends Importer
{
    public const IMPORT_NAME = 'Customer Chatbot Categories';

    protected static ?string $model = CustomerAdvisorCategory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(function (CustomerAdvisorCategoryImporter $importer): array {
                    return [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('customer_advisor_categories', 'name')
                            ->where('customer_advisor_id', $importer->getCustomerAdvisorId()),
                    ];
                })
                ->requiredMapping()
                ->example('Admissions'),
            ImportColumn::make('description')
                ->rules(['required', 'string', 'max:65535'])
                ->requiredMapping()
                ->example('Admissions and enrollment-related FAQs.'),
        ];
    }

    public function resolveRecord(): CustomerAdvisorCategory
    {
        $category = new CustomerAdvisorCategory();
        $category->customerAdvisor()->associate($this->getCustomerAdvisorId());

        return $category;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your ' . static::IMPORT_NAME . ' import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function getCustomerAdvisorId(): string
    {
        $id = $this->options['customer_advisor_id'] ?? null;

        if (blank($id)) {
            throw new InvalidArgumentException('The customer_advisor_id option is required for this import.');
        }

        return (string) $id;
    }
}
