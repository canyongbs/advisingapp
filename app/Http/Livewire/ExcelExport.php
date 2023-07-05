<?php

namespace App\Http\Livewire;

use App\Exports\ModelExport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Livewire\Component;
use Maatwebsite\Excel\Excel;

class ExcelExport extends Component
{
    public $model;

    public $format;

    public function render()
    {
        return view('livewire.excel-export');
    }

    public function export()
    {
        $validatedFormat = $this->validateExportType();

        return (new ModelExport($this->getModel()))->download($this->filename, $validatedFormat);
    }

    public function getFilenameProperty()
    {
        return Str::snake(sprintf('export%s.%s', $this->model, $this->format));
    }

    public function validateExportType()
    {
        $formats = config('excel.extension_detector');

        abort_if(in_array($this->format, $formats), Response::HTTP_NOT_FOUND);

        return $formats[$this->format];
    }

    protected function getModel(): Model
    {
        return app(sprintf('%s\\%s', 'App\Models', $this->model));
    }
}
