<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        $this->processTable('email_templates', 'content');
        $this->processTable('engagements', 'body');
        $this->processTable('engagement_batches', 'body');
        $this->processTable('workflow_engagement_email_details', 'body');
        $this->processNestedJsonTable('campaign_actions', 'data', 'body');
    }

    public function down(): void {}

    protected function processTable(string $table, string $column): void
    {
        DB::table($table)
            ->whereNotNull($column)
            ->eachById(function (object $record) use ($table, $column) {
                $body = json_decode($record->{$column}, associative: true);

                if (! is_array($body)) {
                    return;
                }

                $changed = false;

                $this->processNodes($body, $changed);

                if (! $changed) {
                    return;
                }

                DB::table($table)
                    ->where('id', $record->id)
                    ->update([$column => json_encode($body)]);
            }, 100);
    }

    protected function processNestedJsonTable(string $table, string $column, string $nestedKey): void
    {
        DB::table($table)
            ->whereNotNull($column)
            ->eachById(function (object $record) use ($table, $column, $nestedKey) {
                $data = json_decode($record->{$column}, associative: true);

                if (! is_array($data) || ! isset($data[$nestedKey]) || ! is_array($data[$nestedKey])) {
                    return;
                }

                $changed = false;

                $this->processNodes($data[$nestedKey], $changed);

                if (! $changed) {
                    return;
                }

                DB::table($table)
                    ->where('id', $record->id)
                    ->update([$column => json_encode($data)]);
            }, 100);
    }

    /**
     * @param array<mixed> $node
     */
    protected function processNodes(array &$node, bool &$changed): void
    {
        if (($node['type'] ?? null) === 'image') {
            $width = $node['attrs']['width'] ?? null;

            if (is_numeric($width) && $width > 500) {
                $node['attrs']['width'] = null;
                $node['attrs']['height'] = null;
                $changed = true;
            }
        }

        if (isset($node['marks']) && is_array($node['marks'])) {
            $this->processMarks($node['marks'], $changed);
        }

        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as &$child) {
                if (is_array($child)) {
                    $this->processNodes($child, $changed);
                }
            }
        }
    }

    /**
     * @param array<int, array<mixed>> $marks
     */
    protected function processMarks(array &$marks, bool &$changed): void
    {
        foreach ($marks as &$mark) {
            if (($mark['type'] ?? null) === 'textStyle') {
                $mark['type'] = 'textColor';
                $mark['attrs'] = [
                    'data-color' => $mark['attrs']['color'] ?? null,
                ];
                $changed = true;
            }
        }
    }
};
