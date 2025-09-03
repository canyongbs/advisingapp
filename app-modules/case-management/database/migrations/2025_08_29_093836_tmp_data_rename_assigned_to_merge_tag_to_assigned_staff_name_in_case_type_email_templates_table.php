<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
