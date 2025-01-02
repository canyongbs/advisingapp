<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Application\Exports;

use AdvisingApp\Application\Models\ApplicationField;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ApplicationSubmissionExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected Collection $submissions) {}

    public function collection(): Collection
    {
        return $this->submissions->load(['fields', 'submissible.fields']);
    }

    public function headings(): array
    {
        $submissible = $this->submissions->first()?->submissible;

        return [
            'id',
            'application_id',
            ...$submissible?->fields()->pluck('label')->all() ?? [],
            'created_at',
            'updated_at',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->application_id,
            ...$row->submissible->fields
                ->map(fn (ApplicationField $field) => $row->fields->where('id', $field->id)->first()?->pivot->response)
                ->all(),
            $row->created_at,
            $row->updated_at,
        ];
    }
}
