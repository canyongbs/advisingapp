<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $this->fixFormJsonContent(table: 'applications', from: 'checkbox', to: 'agreement');
        $this->fixFormJsonContent(table: 'application_steps', from: 'checkbox', to: 'agreement');
        $this->fixFieldTypeColumn(table: 'application_fields', from: 'checkbox', to: 'agreement');

        $this->fixFormJsonContent(table: 'event_registration_forms', from: 'checkbox', to: 'agreement');
        $this->fixFormJsonContent(table: 'event_registration_form_steps', from: 'checkbox', to: 'agreement');
        $this->fixFieldTypeColumn(table: 'event_registration_form_fields', from: 'checkbox', to: 'agreement');

        $this->fixFormJsonContent(table: 'forms', from: 'checkbox', to: 'agreement');
        $this->fixFormJsonContent(table: 'form_steps', from: 'checkbox', to: 'agreement');
        $this->fixFieldTypeColumn(table: 'form_fields', from: 'checkbox', to: 'agreement');

        $this->fixFormJsonContent(table: 'service_request_forms', from: 'checkbox', to: 'agreement');
        $this->fixFormJsonContent(table: 'service_request_form_steps', from: 'checkbox', to: 'agreement');
        $this->fixFieldTypeColumn(table: 'service_request_form_fields', from: 'checkbox', to: 'agreement');

        $this->fixFormJsonContent(table: 'surveys', from: 'checkbox', to: 'checkboxes');
        $this->fixFormJsonContent(table: 'survey_steps', from: 'checkbox', to: 'checkboxes');
        $this->fixFieldTypeColumn(table: 'survey_fields', from: 'checkbox', to: 'checkboxes');
    }

    protected function fixFormJsonContent(string $table, string $from, string $to): void
    {
        DB::table($table)
            ->lazyById(100)
            ->each(function (stdClass $record) use ($table, $from, $to) {
                $originalContent = $record->content;

                $content = json_decode($record->content, associative: true) ?? [];

                foreach (($content['content'] ?? []) as $blockIndex => $block) {
                    $content['content'][$blockIndex] = $this->fixTipTapBlock($block, $from, $to);
                }

                $record->content = json_encode($content);

                if ($record->content === $originalContent) {
                    return;
                }

                DB::table($table)
                    ->where('id', $record->id)
                    ->update([
                        'content' => $record->content,
                    ]);
            });
    }

    protected function fixTipTapBlock(array $block, string $from, string $to): array
    {
        foreach (($block['content'] ?? []) as $childBlockIndex => $childBlock) {
            $block['content'][$childBlockIndex] = $this->fixTipTapBlock($childBlock, $from, $to);
        }

        if (($block['type'] ?? null) !== 'tiptapBlock') {
            return $block;
        }

        if (($block['attrs']['type'] ?? null) !== $from) {
            return $block;
        }

        $block['attrs']['type'] = $to;

        return $block;
    }

    protected function fixFieldTypeColumn(string $table, string $from, string $to): void
    {
        DB::table($table)
            ->where('type', $from)
            ->update([
                'type' => $to,
            ]);
    }
};
