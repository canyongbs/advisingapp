<?php

namespace App\Http\Controllers\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SpreadsheetReader;

trait WithCSVImport
{
    public $csvImportModel;

    private int $csvPreviewLineCount = 5;

    private array $csvContent = [];

    private array $csvHeader;

    private bool $csvHasHeader = false;

    public function csvStore(Request $request)
    {
        $request->validate([
            'csv_file'   => 'required|file|mimes:csv,txt',
            'has_header' => 'required|boolean',
        ]);

        $csvFile              = $request->file('csv_file');
        $this->csvHasHeader   = (bool) $request->input('has_header');
        $this->csvImportModel = new $this->csvImportModel();

        $this->makePreview($csvFile->path(), $this->csvPreviewLineCount);

        $storedCsvFile = $csvFile->storeAs('csv_import', sprintf('%s.csv', bin2hex(random_bytes(16))));
        $route         = $request->input('route');

        return view('admin.csv-import', [
            'csvHeader'       => $this->csvHeader,
            'csvPreviewLines' => $this->csvContent,
            'csvHasHeader'    => $this->csvHasHeader,
            'fillables'       => $this->csvImportModel->getFillable(),
            'filename'        => basename($storedCsvFile),
            'route'           => $route,
            'redirectTo'      => url()->previous(),
        ]);
    }

    public function csvUpdate(Request $request)
    {
        $this->csvImportModel = new $this->csvImportModel();
        $this->csvHasHeader   = (bool) $request->input('has_header');

        $this->importEntries($request->input('filename'), $request->input('fields', []));

        return redirect($request->input('redirectTo'));
    }

    private function importEntries(string $filename, $fields): void
    {
        $path   = storage_path('app/csv_import/' . basename($filename));
        $reader = new SpreadsheetReader($path);
        $fields = array_flip(array_filter($fields));

        foreach ($reader as $rowNumber => $row) {
            if ($this->csvHasHeader && $rowNumber == 0) {
                continue;
            }

            $newEntry = [];

            foreach ($fields as $header => $columnNumber) {
                if (isset($row[$columnNumber])) {
                    $newEntry[$header] = $row[$columnNumber];

                    $this->fillOwnerIdField($newEntry);
                }
            }

            if ($this->rowHasEntries($newEntry)) {
                $this->csvContent[] = $newEntry;
            }
        }

        foreach (array_chunk($this->csvContent, 100) as $chunk) {
            $this->csvImportModel::insert($chunk);
        }

        File::delete($path);

        session()->flash(
            'status',
            trans(
                'global.app_imported_rows_to_table',
                [
                    'rows'  => count($this->csvContent),
                    'table' => $this->csvImportModel->table,
                ]
            )
        );
    }

    private function fillOwnerIdField(array &$entry): void
    {
        if (! $this->hasOwnerId()) {
            return;
        }

        $entry['owner_id'] = auth()->id();
    }

    private function hasOwnerId(): bool
    {
        return $this->modelHasTenantableTrait()
            && $this->modelHasOwnerRelationship()
            && ! $this->userModelHasTeam();
    }

    private function modelHasOwnerRelationship(): bool
    {
        return $this->csvImportModel->isRelation('owner');
    }

    private function userModelHasTeam()
    {
        return in_array('App\Traits\HasTeam', class_uses(User::class), true);
    }

    private function modelHasTenantableTrait(): bool
    {
        return in_array('App\Traits\Tenantable', class_uses($this->csvImportModel), true);
    }

    private function rowHasEntries(array $row): bool
    {
        return count($row) > 0;
    }

    private function makePreview(string $path, int $limit = 0): void
    {
        $reader = new SpreadsheetReader($path);

        $this->csvHeader = $reader->current();

        if (! $this->csvHasHeader) {
            $this->csvContent[] = $this->csvHeader;
        }

        $i = 0;
        while ($reader->next() !== false && ($i < $limit || $limit === 0)) {
            $this->csvContent[] = $reader->current();
            $i++;
        }
    }
}
