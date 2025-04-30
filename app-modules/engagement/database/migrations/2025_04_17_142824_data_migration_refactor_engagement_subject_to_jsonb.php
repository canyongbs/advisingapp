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

use App\Features\RefactorEngagementCampaignSubjectToJsonb;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('engagements')->select('id', 'subject')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $json = [
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null],
                            'content' => [[
                                'type' => 'text',
                                'text' => $row->subject,
                            ]],
                        ]],
                    ];
                    DB::table('engagements')
                        ->where('id', $row->id)
                        ->update(['subject' => json_encode($json)]);
                }
            });

            DB::table('engagement_batches')->select('id', 'subject')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $json = [
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null],
                            'content' => [[
                                'type' => 'text',
                                'text' => $row->subject,
                            ]],
                        ]],
                    ];
                    DB::table('engagement_batches')
                        ->where('id', $row->id)
                        ->update(['subject' => json_encode($json)]);
                }
            });

            DB::table('form_email_auto_replies')->select('id', 'subject')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $json = [
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null],
                            'content' => [[
                                'type' => 'text',
                                'text' => $row->subject,
                            ]],
                        ]],
                    ];
                    DB::table('form_email_auto_replies')
                        ->where('id', $row->id)
                        ->update(['subject' => json_encode($json)]);
                }
            });

            DB::table('campaign_actions')
                ->select('id', 'data')
                ->where('type', 'bulk_engagement_email')
                ->whereRaw("json_typeof(data::json -> 'subject') = 'string'")
                ->chunkById(500, function ($rows) {
                    foreach ($rows as $row) {
                        $data = json_decode($row->data, true);

                        $json = [
                            'channel' => $data['channel'] ?? null,
                            'subject' => [
                                'type' => 'doc',
                                'content' => [[
                                    'type' => 'paragraph',
                                    'attrs' => ['class' => null, 'style' => null],
                                    'content' => [[
                                        'type' => 'text',
                                        'text' => $data['subject'] ?? '',
                                    ]],
                                ]],
                            ],
                            'body' => $data['body'] ?? null,
                        ];

                        DB::table('campaign_actions')
                            ->where('id', $row->id)
                            ->update(['data' => json_encode($json)]);
                    }
                });

            RefactorEngagementCampaignSubjectToJsonb::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('engagements')->select('id', 'subject')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    /** @var array<string, mixed> $decoded */
                    $decoded = json_decode($row->subject, true);

                    /** @var array<int, array{type: string, content?: array<int, array{type: string, text: string}>}> $blocks */
                    $blocks = $decoded['content'] ?? [];

                    /** @var string $text */
                    $text = collect($blocks)
                        ->flatMap(fn ($block) => collect($block['content'] ?? []))
                        ->filter(fn ($node) => $node['type'] === 'text')
                        ->pluck('text')
                        ->implode('');

                    DB::table('engagements')
                        ->where('id', $row->id)
                        ->update(['subject' => $text]);
                }
            });

            DB::table('engagement_batches')->select('id', 'subject')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    /** @var array<string, mixed> $decoded */
                    $decoded = json_decode($row->subject, true);

                    /** @var array<int, array{type: string, content?: array<int, array{type: string, text: string}>}> $blocks */
                    $blocks = $decoded['content'] ?? [];

                    /** @var string $text */
                    $text = collect($blocks)
                        ->flatMap(fn ($block) => collect($block['content'] ?? []))
                        ->filter(fn ($node) => $node['type'] === 'text')
                        ->pluck('text')
                        ->implode('');

                    DB::table('engagement_batches')
                        ->where('id', $row->id)
                        ->update(['subject' => $text]);
                }
            });

            DB::table('form_email_auto_replies')->select('id', 'subject')->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    /** @var array<string, mixed> $decoded */
                    $decoded = json_decode($row->subject, true);

                    /** @var array<int, array{type: string, content?: array<int, array{type: string, text: string}>}> $blocks */
                    $blocks = $decoded['content'] ?? [];

                    /** @var string $text */
                    $text = collect($blocks)
                        ->flatMap(fn ($block) => collect($block['content'] ?? []))
                        ->filter(fn ($node) => $node['type'] === 'text')
                        ->pluck('text')
                        ->implode('');

                    DB::table('form_email_auto_replies')
                        ->where('id', $row->id)
                        ->update(['subject' => $text]);
                }
            });

            DB::table('campaign_actions')
                ->select('id', 'data')
                ->where('type', 'bulk_engagement_email')
                ->whereRaw("json_typeof(data::json -> 'subject') = 'object'")
                ->whereRaw("(data::json -> 'subject' ->> 'type') = 'doc'")
                ->chunkById(500, function ($rows) {
                    foreach ($rows as $row) {
                        $data = json_decode($row->data, true);

                        $json = [
                            'channel' => $data['channel'] ?? null,
                            'subject' => $data['subject']['content'][0]['content'][0]['text'] ?? '',
                            'body' => $data['body'] ?? null,
                        ];

                        DB::table('campaign_actions')
                            ->where('id', $row->id)
                            ->update(['data' => json_encode($json)]);
                    }
                });

            RefactorEngagementCampaignSubjectToJsonb::deactivate();
        });
    }
};
