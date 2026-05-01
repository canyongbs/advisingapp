<?php

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
