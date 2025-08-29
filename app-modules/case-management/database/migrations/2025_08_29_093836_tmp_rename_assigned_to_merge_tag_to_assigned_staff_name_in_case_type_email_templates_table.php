<?php

use App\Features\AssignedToMergeTagRenameFeatureFlag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('case_type_email_templates')
                ->chunkById(100, function (Collection $templates) {
                    foreach ($templates as $template) {
                        $updateData = [];

                        if ($template->subject && $this->containsAssignedToMergeTag($template->subject)) {
                            $updateData['subject'] = $this->updateMergeTag($template->subject);
                        }

                        if ($template->body && $this->containsAssignedToMergeTag($template->body)) {
                            $updateData['body'] = $this->updateMergeTag($template->body);
                        }

                        if (! empty($updateData)) {
                            DB::table('case_type_email_templates')
                                ->where('id', $template->id)
                                ->update($updateData);
                        }
                    }
                });

            AssignedToMergeTagRenameFeatureFlag::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('case_type_email_templates')
                ->chunkById(100, function (Collection $templates) {
                    foreach ($templates as $template) {
                        $updateData = [];

                        if ($template->subject && $this->containsAssignedStaffNameMergeTag($template->subject)) {
                            $updateData['subject'] = $this->revertMergeTag($template->subject);
                        }

                        if ($template->body && $this->containsAssignedStaffNameMergeTag($template->body)) {
                            $updateData['body'] = $this->revertMergeTag($template->body);
                        }

                        if (! empty($updateData)) {
                            DB::table('case_type_email_templates')
                                ->where('id', $template->id)
                                ->update($updateData);
                        }
                    }
                });

            AssignedToMergeTagRenameFeatureFlag::deactivate();
        });
    }

    private function updateMergeTag(?string $content): ?string
    {
        if (! $content) {
            return $content;
        }

        return str_replace(
            '{"type": "mergeTag", "attrs": {"id": "assigned to"}',
            '{"type": "mergeTag", "attrs": {"id": "assigned staff name"}',
            $content
        );
    }

    private function revertMergeTag(?string $content): ?string
    {
        if (! $content) {
            return $content;
        }

        return str_replace(
            '{"type": "mergeTag", "attrs": {"id": "assigned staff name"}',
            '{"type": "mergeTag", "attrs": {"id": "assigned to"}',
            $content
        );
    }

    private function containsAssignedToMergeTag(string $content): bool
    {
        return str_contains($content, '{"type": "mergeTag", "attrs": {"id": "assigned to"}');
    }

    private function containsAssignedStaffNameMergeTag(string $content): bool
    {
        return str_contains($content, '{"type": "mergeTag", "attrs": {"id": "assigned staff name"}');
    }
};
