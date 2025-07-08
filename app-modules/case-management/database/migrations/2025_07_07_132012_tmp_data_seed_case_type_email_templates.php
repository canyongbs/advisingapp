<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('case_types')->chunkById(100, function (Collection $caseTypes) {
                foreach ($caseTypes as $caseType) {
                    $roles = ['manager', 'auditor', 'customer'];
                    $templates = ['created', 'assigned', 'update', 'status_change', 'closed'];

                    foreach ($templates as $template) {
                        foreach ($roles as $role) {
                            $subject = $this->getSubject($template, $role);
                            $body = $this->getBody($template, $role);

                            $existing = DB::table('case_type_email_templates')
                                ->where('case_type_id', $caseType->id)
                                ->where('type', $template)
                                ->where('role', $role)
                                ->first();

                            if ($existing) {
                                DB::table('case_type_email_templates')
                                    ->where('id', $existing->id)
                                    ->update([
                                        'subject' => json_encode($subject),
                                        'body' => json_encode($body),
                                        'updated_at' => now(),
                                    ]);
                            } else {
                                DB::table('case_type_email_templates')->insert([
                                    'id' => (string) Str::orderedUuid(),
                                    'case_type_id' => $caseType->id,
                                    'type' => $template,
                                    'role' => $role,
                                    'subject' => json_encode($subject),
                                    'body' => json_encode($body),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            });
        });
    }

    public function down(): void
    {
        DB::table('case_type_email_templates')->truncate();
    }

    /**
     * @param string $template
     * @param string $role
     *
     * @return array<string, mixed>
     */
    private function getSubject(string $template, string $role): array
    {
        $subjects = [
            'created' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' created', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' created', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' - is now ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
            'assigned' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' assigned to agent', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' assigned to agent', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Your case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' has been assigned to agent', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
            'update' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'New update on case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'New update on case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => "There's an update on your case ", 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
            'status_change' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' status changed to ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' status changed to ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Status update: case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' is now ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
            'closed' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' closed', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'case ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ' closed', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => '[Ticket #', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                            ['text' => ']: Your Issue Has Been Resolved ', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
        ];

        return $subjects[$template][$role] ?? [];
    }

    /**
     * @param string $template
     * @param string $role
     *
     * @return array<string, mixed>
     */
    private function getBody(string $template, string $role): array
    {
        $bodies = [
            'created' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has been created.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has been created.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Hello ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'A new ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'type']],
                                ['text' => ' case has been created and is now in a ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => ' status. Your new ticket number is: ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'The details of your case are shown below:', 'type' => 'text']],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'description']],
                            ],
                        ],
                    ],
                ],
            ],
            'assigned' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has been assigned to an agent.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has been assigned to an agent.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Hello ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => "We've assigned an agent to your ", 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => '. They will review it and follow up shortly.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
            ],
            'update' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has a new update.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has a new update.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Hello ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => "There's been a new update to your case ", 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => '. Please check the latest details.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
            ],
            'status_change' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' status has changed to ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' status has changed to ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Hello ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The status of your case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has been updated to: ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'You can view your request for more details.', 'type' => 'text']],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
            ],
            'closed' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has been closed.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The case ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' has been closed.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'caseRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Dear ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'We wanted to update you that the issue you reported in Ticket #', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'case number']],
                                ['text' => ' regarding ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'description']],
                                ['text' => ' has been ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [[
                                'text' => 'If you experience any further issues or have additional questions, please do not hesitate to open a new ticket.',
                                'type' => 'text',
                            ]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [[
                                'text' => 'Thank you for giving us a chance to help you with your issue.',
                                'type' => 'text',
                            ]],
                        ],
                    ],
                ],
            ],
        ];

        return $bodies[$template][$role] ?? [];
    }
};
