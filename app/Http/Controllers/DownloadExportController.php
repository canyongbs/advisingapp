<?php

namespace App\Http\Controllers;

use App\Models\Export;
use Filament\Actions\Exports\Downloaders\CsvDownloader;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadExportController extends Controller
{
    public function __invoke(Export $export): StreamedResponse
    {
        abort_unless(auth()->user()->can('export_hub.import'), 403);

        return app(CsvDownloader::class)($export);
    }
}
