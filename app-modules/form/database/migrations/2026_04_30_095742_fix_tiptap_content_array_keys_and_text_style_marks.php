<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        $this->processTable('forms', 'content');
        $this->processTable('form_steps', 'content');
        $this->processTable('surveys', 'content');
        $this->processTable('survey_steps', 'content');
        $this->processTable('applications', 'content');
        $this->processTable('application_steps', 'content');
    }

    public function down(): void
    {
        // This is a data migration and cannot be reversed
    }

    protected function processTable(string $table, string $column): void
    {
        DB::table($table)
            ->whereNotNull($column)
            ->eachById(function (object $record) use ($table, $column) {
                $content = json_decode($record->{$column}, associative: true);

                if (! is_array($content)) {
                    return;
                }

                $changed = false;

                $this->fixContentArrays($content, $changed);
                $this->stripTextStyleMarks($content, $changed);

                if (! $changed) {
                    return;
                }

                DB::table($table)
                    ->where('id', $record->id)
                    ->update([$column => json_encode($content)]);
            }, 100);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function fixContentArrays(array &$node, bool &$changed): void
    {
        if (isset($node['content']) && is_array($node['content']) && ! array_is_list($node['content'])) {
            $node['content'] = array_values($node['content']);
            $changed = true;
        }

        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as &$child) {
                if (is_array($child)) {
                    $this->fixContentArrays($child, $changed);
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function stripTextStyleMarks(array &$node, bool &$changed): void
    {
        if (isset($node['marks']) && is_array($node['marks'])) {
            $filtered = array_values(array_filter($node['marks'], fn (array $mark) => ($mark['type'] ?? null) !== 'textStyle'));

            if (count($filtered) !== count($node['marks'])) {
                $changed = true;

                if (empty($filtered)) {
                    unset($node['marks']);
                } else {
                    $node['marks'] = $filtered;
                }
            }
        }

        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as &$child) {
                if (is_array($child)) {
                    $this->stripTextStyleMarks($child, $changed);
                }
            }
        }
    }
};
