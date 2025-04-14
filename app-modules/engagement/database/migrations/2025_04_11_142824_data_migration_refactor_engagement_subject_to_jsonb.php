<?php

use App\Features\RefactorEngagementCampaignSubjectToJsonb;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('engagements')->select('id', 'subject')->orderBy('id')->chunk(500, function ($rows) {
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

            DB::table('engagement_batches')->select('id', 'subject')->orderBy('id')->chunk(500, function ($rows) {
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

            DB::table('campaign_actions')
                ->select('id', 'data')
                ->whereRaw("json_typeof(data::json -> 'subject') = 'string'")
                ->orderBy('id')
                ->chunk(500, function ($rows) {
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
            DB::table('engagements')->select('id', 'subject')->orderBy('id')->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    $decoded = json_decode($row->subject, true);
                    $text = $decoded['content'][0]['content'][0]['text'] ?? '';
                    DB::table('engagements')
                        ->where('id', $row->id)
                        ->update(['subject' => $text]);
                }
            });

            DB::table('engagement_batches')->select('id', 'subject')->orderBy('id')->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    $decoded = json_decode($row->subject, true);
                    $text = $decoded['content'][0]['content'][0]['text'] ?? '';
                    DB::table('engagement_batches')
                        ->where('id', $row->id)
                        ->update(['subject' => $text]);
                }
            });

            DB::table('campaign_actions')
                ->select('id', 'data')
                ->whereRaw("json_typeof(data::json -> 'subject') = 'object'")
                ->whereRaw("(data::json -> 'subject' ->> 'type') = 'doc'")
                ->orderBy('id')
                ->chunk(500, function ($rows) {
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
