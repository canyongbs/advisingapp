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

use AdvisingApp\Application\Models\ApplicationEmailAutoReply;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;

/**
 * @param  array<int, array<string, mixed>>  $content
 *
 * @return array<string, mixed>
 */
$tiptapDoc = function (array $content): array {
    return ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => $content]]];
};

/** @return array<string, mixed> */
$tiptapText = function (string $text, array $marks = []): array {
    return ['type' => 'text', 'text' => $text, ...($marks ? ['marks' => $marks] : [])];
};

/** @return array<string, mixed> */
$tiptapMergeTag = function (string $id): array {
    return ['type' => 'mergeTag', 'attrs' => ['id' => $id]];
};

it('fills the recipient merge tags from the author', function (Student|Prospect $author) use ($tiptapDoc, $tiptapText, $tiptapMergeTag) {
    $emailAutoReply = ApplicationEmailAutoReply::factory()->make([
        'application_id' => null,
        'subject' => $tiptapDoc([
            $tiptapText('Hi '),
            $tiptapMergeTag('recipient first name'),
            $tiptapText(' '),
            $tiptapMergeTag('recipient last name'),
        ]),
        'body' => $tiptapDoc([
            $tiptapMergeTag('recipient full name'),
            $tiptapText(' / '),
            $tiptapMergeTag('recipient preferred name'),
            $tiptapText(' / '),
            $tiptapMergeTag('recipient email'),
        ]),
    ]);

    expect($emailAutoReply->getSubject($author))->toBe('Hi Jamie Rivera')
        ->and(html_entity_decode($emailAutoReply->getBody($author)))
        ->toContain('Jamie Rivera')
        ->toContain('Jay')
        ->toContain($author->primaryEmailAddress?->address);
})->with([
    'student' => fn () => Student::factory()->create([
        'first' => 'Jamie',
        'last' => 'Rivera',
        'full_name' => 'Jamie Rivera',
        'preferred' => 'Jay',
    ]),
    'prospect' => fn () => Prospect::factory()->create([
        'first_name' => 'Jamie',
        'last_name' => 'Rivera',
        'full_name' => 'Jamie Rivera',
        'preferred' => 'Jay',
    ]),
]);

it('leaves the recipient merge tags empty when there is no author', function () use ($tiptapDoc, $tiptapText, $tiptapMergeTag) {
    $emailAutoReply = ApplicationEmailAutoReply::factory()->make([
        'application_id' => null,
        'subject' => $tiptapDoc([
            $tiptapText('Hi '),
            $tiptapMergeTag('recipient first name'),
        ]),
        'body' => $tiptapDoc([
            $tiptapText('Thanks '),
            $tiptapMergeTag('recipient email'),
        ]),
    ]);

    expect($emailAutoReply->getSubject(null))->toBe('Hi')
        ->and($emailAutoReply->getBody(null))->toContain('Thanks');
});

it('returns the subject as plain text', function () use ($tiptapDoc, $tiptapText) {
    $emailAutoReply = ApplicationEmailAutoReply::factory()->make([
        'application_id' => null,
        'subject' => $tiptapDoc([
            $tiptapText('Fish & Chips', [['type' => 'bold']]),
            $tiptapText("   extra \n  spaces"),
        ]),
    ]);

    expect($emailAutoReply->getSubject(null))->toBe('Fish & Chips extra spaces');
});

it('returns empty strings when the subject and body are empty', function () {
    $emailAutoReply = ApplicationEmailAutoReply::factory()->make([
        'application_id' => null,
        'subject' => null,
        'body' => null,
    ]);

    expect($emailAutoReply->getSubject(null))->toBe('')
        ->and($emailAutoReply->getBody(null))->toBe('');
});
