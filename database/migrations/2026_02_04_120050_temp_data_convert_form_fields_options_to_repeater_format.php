<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            $updateFieldsConfig = function (string $table) {
                DB::table($table)
                    ->whereIn('type', ['radio', 'checkboxes'])
                    ->chunkById(100, function (Collection $fields) use ($table) {
                        foreach ($fields as $field) {
                            /** @var stdClass $field */
                            $config = json_decode($field->config, true);

                            if (! is_array($config) || ! isset($config['options']) || ! is_array($config['options'])) {
                                continue;
                            }

                            $options = $config['options'];

                            if (empty($options)) {
                                continue;
                            }

                            $firstKey = array_key_first($options);
                            $firstOption = $options[$firstKey];

                            if (is_array($firstOption) && isset($firstOption['value']) && isset($firstOption['label'])) {
                                continue;
                            }

                            $newOptions = [];

                            foreach ($options as $value => $label) {
                                $newOptions[] = [
                                    'value' => (string) $value,
                                    'label' => $label,
                                ];
                            }

                            $config['options'] = $newOptions;

                            DB::table($table)
                                ->where('id', $field->id)
                                ->update(['config' => json_encode($config)]);
                        }
                    });
            };

            $updateFieldsConfig('form_fields');
            $updateFieldsConfig('application_fields');
            $updateFieldsConfig('event_registration_form_fields');

            $processJsonColumn = function (string $table, string $column) {
                DB::table($table)
                    ->whereNotNull($column)
                    ->chunkById(100, function (Collection $records) use ($table, $column) {
                        foreach ($records as $record) {
                            /** @var stdClass $record */
                            $content = json_decode($record->{$column}, true);

                            if (! is_array($content) || ! isset($content['content']) || ! is_array($content['content'])) {
                                continue;
                            }

                            $modified = false;

                            $traverse = function (array &$blocks) use (&$traverse, &$modified) {
                                foreach ($blocks as &$block) {
                                    if (isset($block['type']) && $block['type'] === 'tiptapBlock' && isset($block['attrs']['type']) && in_array($block['attrs']['type'], ['radio', 'checkboxes'])) {
                                        if (isset($block['attrs']['data']['options']) && is_array($block['attrs']['data']['options'])) {
                                            $options = $block['attrs']['data']['options'];

                                            if (! empty($options)) {
                                                $firstKey = array_key_first($options);
                                                $firstOption = $options[$firstKey];

                                                if (! (is_array($firstOption) && isset($firstOption['value']) && isset($firstOption['label']))) {
                                                    $newOptions = [];

                                                    foreach ($options as $value => $label) {
                                                        $newOptions[] = [
                                                            'value' => (string) $value,
                                                            'label' => $label,
                                                        ];
                                                    }
                                                    $block['attrs']['data']['options'] = $newOptions;
                                                    $modified = true;
                                                }
                                            }
                                        }
                                    }

                                    if (isset($block['content']) && is_array($block['content'])) {
                                        $traverse($block['content']);
                                    }
                                }
                            };

                            $traverse($content['content']);

                            if ($modified) {
                                DB::table($table)
                                    ->where('id', $record->id)
                                    ->update([$column => json_encode($content)]);
                            }
                        }
                    });
            };

            $processJsonColumn('forms', 'content');
            $processJsonColumn('applications', 'content');
            $processJsonColumn('event_registration_forms', 'content');
            $processJsonColumn('form_steps', 'content');
            $processJsonColumn('application_steps', 'content');
            $processJsonColumn('event_registration_form_steps', 'content');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $revertFieldsConfig = function (string $table) {
                DB::table($table)
                    ->whereIn('type', ['radio', 'checkboxes'])
                    ->chunkById(100, function (Collection $fields) use ($table) {
                        foreach ($fields as $field) {
                            /** @var stdClass $field */
                            $config = json_decode($field->config, true);

                            if (! is_array($config) || ! isset($config['options']) || ! is_array($config['options'])) {
                                continue;
                            }

                            $options = $config['options'];

                            if (empty($options) || ! isset($options[0]) || ! is_array($options[0]) || ! isset($options[0]['value'])) {
                                continue;
                            }

                            $oldOptions = [];

                            foreach ($options as $option) {
                                if (isset($option['value']) && isset($option['label'])) {
                                    $oldOptions[$option['value']] = $option['label'];
                                }
                            }

                            $config['options'] = $oldOptions;

                            DB::table($table)
                                ->where('id', $field->id)
                                ->update(['config' => json_encode($config)]);
                        }
                    });
            };

            $revertFieldsConfig('form_fields');
            $revertFieldsConfig('application_fields');
            $revertFieldsConfig('event_registration_form_fields');

            $revertJsonColumn = function (string $table, string $column) {
                DB::table($table)
                    ->whereNotNull($column)
                    ->chunkById(100, function (Collection $records) use ($table, $column) {
                        foreach ($records as $record) {
                            /** @var stdClass $record */
                            $content = json_decode($record->{$column}, true);

                            if (! is_array($content) || ! isset($content['content']) || ! is_array($content['content'])) {
                                continue;
                            }

                            $modified = false;

                            $traverse = function (array &$blocks) use (&$traverse, &$modified) {
                                foreach ($blocks as &$block) {
                                    if (isset($block['type']) && $block['type'] === 'tiptapBlock' && isset($block['attrs']['type']) && in_array($block['attrs']['type'], ['radio', 'checkboxes'])) {
                                        if (isset($block['attrs']['data']['options']) && is_array($block['attrs']['data']['options'])) {
                                            $options = $block['attrs']['data']['options'];

                                            if (! empty($options) && isset($options[0]) && is_array($options[0]) && isset($options[0]['value'])) {
                                                $oldOptions = [];

                                                foreach ($options as $option) {
                                                    if (isset($option['value']) && isset($option['label'])) {
                                                        $oldOptions[$option['value']] = $option['label'];
                                                    }
                                                }
                                                $block['attrs']['data']['options'] = $oldOptions;
                                                $modified = true;
                                            }
                                        }
                                    }

                                    if (isset($block['content']) && is_array($block['content'])) {
                                        $traverse($block['content']);
                                    }
                                }
                            };

                            $traverse($content['content']);

                            if ($modified) {
                                DB::table($table)
                                    ->where('id', $record->id)
                                    ->update([$column => json_encode($content)]);
                            }
                        }
                    });
            };

            $revertJsonColumn('forms', 'content');
            $revertJsonColumn('applications', 'content');
            $revertJsonColumn('event_registration_forms', 'content');
            $revertJsonColumn('form_steps', 'content');
            $revertJsonColumn('application_steps', 'content');
            $revertJsonColumn('event_registration_form_steps', 'content');
        });
    }
};
