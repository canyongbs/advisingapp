<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

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
