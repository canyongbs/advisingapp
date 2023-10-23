<?php

namespace Assist\Form\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class FormSubmissionExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected Collection $submissions) {}

    public function collection(): Collection
    {
        return $this->submissions;
    }

    public function headings(): array
    {
        return [
            'id',
            'form_id',
            'content',
            'created_at',
            'updated_at',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->form_id,
            $row->content,
            $row->created_at,
            $row->updated_at,
        ];
    }
}
