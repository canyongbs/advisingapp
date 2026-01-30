<?php

use App\Features\ExportHubFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            $exporters = [
                'AdvisingApp\Ai\Filament\Exports\LegacyAiMessageExporter' => 'AdvisingApp\Ai\Filament\Exports\AssistantUtilizationExporter',
                'AdvisingApp\Report\Filament\Exports\StudentReportTableExporter' => 'AdvisingApp\Report\Filament\Exports\MostRecentStudentsExporter',
                'AdvisingApp\Report\Filament\Exports\MostEngagedStudentTableExporter' => 'AdvisingApp\Report\Filament\Exports\MostActivelyEngagedStudentsExporter',
                'AdvisingApp\Report\Filament\Exports\StudentMessagesDetailTableExporter' => 'AdvisingApp\Report\Filament\Exports\StudentMessagesExporter',
                'AdvisingApp\Report\Filament\Exports\ProspectReportTableExportExporter' => 'AdvisingApp\Report\Filament\Exports\MostRecentProspectsExporter',
                'AdvisingApp\Report\Filament\Exports\ResearchAdvisorReportTableExporter' => 'AdvisingApp\Report\Filament\Exports\ResearchAdvisorExporter',
                'AdvisingApp\Report\Filament\Exports\StudentInteractionUsersTableExportExporter' => 'AdvisingApp\Report\Filament\Exports\StudentInteractionUsersExporter',
            ];

            foreach ($exporters as $oldExporter => $newExporter) {
                DB::table('exports')
                    ->where('exporter', $oldExporter)
                    ->update(['exporter' => $newExporter]);
            }
            ExportHubFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $exporters = [
                'AdvisingApp\Ai\Filament\Exports\AssistantUtilizationExporter' => 'AdvisingApp\Ai\Filament\Exports\LegacyAiMessageExporter',
                'AdvisingApp\Report\Filament\Exports\MostRecentStudentsExporter' => 'AdvisingApp\Report\Filament\Exports\StudentReportTableExporter',
                'AdvisingApp\Report\Filament\Exports\MostActivelyEngagedStudentsExporter' => 'AdvisingApp\Report\Filament\Exports\MostEngagedStudentTableExporter',
                'AdvisingApp\Report\Filament\Exports\StudentMessagesExporter' => 'AdvisingApp\Report\Filament\Exports\StudentMessagesDetailTableExporter',
                'AdvisingApp\Report\Filament\Exports\MostRecentProspectsExporter' => 'AdvisingApp\Report\Filament\Exports\ProspectReportTableExportExporter',
                'AdvisingApp\Report\Filament\Exports\ResearchAdvisorExporter' => 'AdvisingApp\Report\Filament\Exports\ResearchAdvisorReportTableExporter',
                'AdvisingApp\Report\Filament\Exports\StudentInteractionUsersExporter' => 'AdvisingApp\Report\Filament\Exports\StudentInteractionUsersTableExportExporter',
            ];

            foreach ($exporters as $newExporter => $oldExporter) {
                DB::table('exports')
                    ->where('exporter', $newExporter)
                    ->update(['exporter' => $oldExporter]);
            }

            ExportHubFeature::deactivate();
        });
    }
};
