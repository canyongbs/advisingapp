<?php

use AdvisingApp\StudentDataModel\Filament\Imports\StudentEnrollmentImporter;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentImporter;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentProgramImporter;
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
                ->where('importer', StudentImporter::class)
                ->eachById(function (Import $import) {
                    DB::connection('landlord')
                        ->table('job_batches')
                        ->where('name', "student-import-{$import->getKey()}")
                        ->update([
                            'cancelled_at' => time(),
                            'finished_at' => time(),
                        ]);

                    Notification::make()
                        ->title('Import cancelled due to system maintenance')
                        ->body('Your active student import has been cancelled. We apologize for the inconvenience. Please start the import process again, noting the change in how email addresses, phone numbers and addresses are handled.')
                        ->danger()
                        ->sendToDatabase($import->user);
                }, count: 100);

            $tables = DB::table('pg_tables')
                ->select('tablename')
                ->where('schemaname', 'public')
                ->where('tablename', 'like', 'import_%')
                ->get();

            foreach ($tables as $table) {
                if ($table->tablename === 'imports') {
                    continue;
                }

                DB::statement("drop table if exists \"{$table->tablename}\" cascade");
            }

            Import::query()
                ->whereNull('completed_at')
                ->whereIn('importer', [
                    StudentImporter::class,
                    StudentProgramImporter::class,
                    StudentEnrollmentImporter::class,
                ])
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
