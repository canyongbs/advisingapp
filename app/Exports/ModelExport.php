<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ModelExport implements FromCollection, WithHeadings
{
    use Exportable;

    public Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function collection(): Collection
    {
        return $this->model->all();
    }

    public function headings(): array
    {
        return Schema::getColumnListing($this->model->getTable());
    }
}
