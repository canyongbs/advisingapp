<?php

use AdvisingApp\Prospect\Imports\ProspectImporter;
use App\Models\Import;
use Filament\Notifications\Notification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Import::query()
                ->whereNull('completed_at')
                ->where('importer', ProspectImporter::class)
                ->eachById(function (Import $import) {
                    DB::connection('landlord')
                        ->table('job_batches')
                        ->where('name', "prospect-import-{$import->getKey()}")
                        ->update([
                            'cancelled_at' => now(),
                            'finished_at' => now(),
                        ]);

                    Notification::make()
                        ->title('Import cancelled due to system maintenance')
                        ->body('Your active prospect import has been cancelled. We apologize for the inconvenience. Please start the import process again, noting the change in how email addresses, phone numbers and addresses are handled.')
                        ->danger()
                        ->sendToDatabase($import->user);
                }, count: 100);

            Import::query()
                ->whereNull('completed_at')
                ->where('importer', ProspectImporter::class)
                ->update([
                    'completed_at' => now(),
                ]);
        });
    }

    public function down(): void
    {
        // This is not possible to reverse.
    }
};
