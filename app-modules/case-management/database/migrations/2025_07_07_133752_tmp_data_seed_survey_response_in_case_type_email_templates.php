<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('case_type_email_templates')
                ->where('type', 'survey_response')
                ->whereIn('role', ['auditor', 'manager'])
                ->delete();

            $subject = [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'attrs' => [
                            'class' => null,
                            'style' => null,
                            'textAlign' => 'start',
                        ],
                        'content' => [
                            [
                                'text' => 'Feedback survey for ',
                                'type' => 'text',
                            ],
                            [
                                'type' => 'mergeTag',
                                'attrs' => ['id' => 'case number'],
                            ],
                        ],
                    ],
                ],
            ];

            $body = [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'attrs' => [
                            'class' => null,
                            'style' => null,
                            'textAlign' => 'start',
                        ],
                        'content' => [
                            [
                                'text' => 'Hi ',
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'bold'],
                                ],
                            ],
                            [
                                'type' => 'mergeTag',
                                'attrs' => ['id' => 'assigned to'],
                                'marks' => [
                                    ['type' => 'bold'],
                                ],
                            ],
                            [
                                'text' => ',',
                                'type' => 'text',
                                'marks' => [
                                    ['type' => 'bold'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'paragraph',
                        'attrs' => [
                            'class' => null,
                            'style' => null,
                            'textAlign' => 'start',
                        ],
                        'content' => [
                            [
                                'text' => "To help us serve you better in the future, we'd love to hear about your experience with our support team.",
                                'type' => 'text',
                            ],
                        ],
                    ],
                    [
                        'type' => 'tiptapBlock',
                        'attrs' => [
                            'data' => [
                                'alignment' => 'center',
                                'take_survey' => 'Take Survey',
                            ],
                            'type' => 'surveyResponseEmailTemplateTakeSurveyButtonBlock',
                        ],
                    ],
                    [
                        'type' => 'paragraph',
                        'attrs' => [
                            'class' => null,
                            'style' => null,
                            'textAlign' => 'start',
                        ],
                        'content' => [
                            ['type' => 'hardBreak'],
                            [
                                'text' => 'We appreciate your time and we value your feedback!',
                                'type' => 'text',
                            ],
                            ['type' => 'hardBreak'],
                            [
                                'text' => 'Thank You.',
                                'type' => 'text',
                            ],
                            ['type' => 'hardBreak'],
                        ],
                    ],
                ],
            ];

            $subjectJson = json_encode($subject);
            $bodyJson = json_encode($body);

            DB::table('case_types')->orderBy('id')->chunkById(100, function (Collection $types) use ($subjectJson, $bodyJson) {
                foreach ($types as $type) {
                    $existing = DB::table('case_type_email_templates')->where([
                        'case_type_id' => $type->id,
                        'type' => 'survey_response',
                        'role' => 'customer',
                    ])->first();

                    if ($existing) {
                        DB::table('case_type_email_templates')->where('id', $existing->id)->update([
                            'subject' => $subjectJson,
                            'body' => $bodyJson,
                            'updated_at' => now(),
                        ]);
                    } else {
                        DB::table('case_type_email_templates')->insert([
                            'id' => (string) Str::orderedUuid(),
                            'case_type_id' => $type->id,
                            'type' => 'survey_response',
                            'role' => 'customer',
                            'subject' => $subjectJson,
                            'body' => $bodyJson,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });
        });
    }

    public function down(): void
    {
        DB::table('case_type_email_templates')
            ->where('type', 'survey_response')
            ->where('role', 'customer')
            ->delete();
    }
};
