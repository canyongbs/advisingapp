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

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\HtmlString;

/**
 * @param  array<int, array<string, mixed>>  $content
 *
 * @return array<string, mixed>
 */
function tiptapDoc(array $content): array
{
    return ['type' => 'doc', 'content' => $content];
}

/**
 * @param  array<int, array<string, mixed>>  $content
 *
 * @return array<string, mixed>
 */
function tiptapParagraph(array $content): array
{
    return ['type' => 'paragraph', 'content' => $content];
}

/** @return array<string, string> */
function tiptapText(string $text): array
{
    return ['type' => 'text', 'text' => $text];
}

/** @return array<string, mixed> */
function tiptapMergeTag(string $id): array
{
    return ['type' => 'mergeTag', 'attrs' => ['id' => $id]];
}

it('returns the subject as plain text', function () {
    $engagement = Engagement::factory()->forStudent()->email()->create([
        'subject' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Welcome to the program'),
            ]),
        ]),
    ]);

    $subject = $engagement->getSubject();

    expect($subject)
        ->toBeInstanceOf(HtmlString::class)
        ->and((string) $subject)
        ->toBe('Welcome to the program');
});

it('returns null for an empty subject', function () {
    $engagement = Engagement::factory()->forStudent()->email()->create([
        'subject' => tiptapDoc([
            tiptapParagraph([]),
        ]),
    ]);

    expect($engagement->getSubject())->toBeNull();
});

it('returns the body as html', function () {
    $engagement = Engagement::factory()->forStudent()->email()->create([
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Hello world'),
            ]),
        ]),
    ]);

    $body = $engagement->getBody();

    expect($body)
        ->toBeInstanceOf(HtmlString::class)
        ->and((string) $body)
        ->toBe('<p>Hello world</p>');
});

it('returns the body as plain text', function () {
    $engagement = Engagement::factory()->forStudent()->sms()->create([
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Hello world'),
            ]),
        ]),
    ]);

    expect($engagement->getBodyText())
        ->toBe('Hello world');
});

it('decodes html entities in body text', function () {
    $engagement = Engagement::factory()->forStudent()->sms()->create([
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Tom & Jerry\'s "adventure" <today>'),
            ]),
        ]),
    ]);

    expect($engagement->getBodyText())
        ->toBe('Tom & Jerry\'s "adventure" <today>');
});

it('decodes html entities in subject', function () {
    $engagement = Engagement::factory()->forStudent()->email()->create([
        'subject' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Q&A session: "What\'s next?"'),
            ]),
        ]),
    ]);

    expect((string) $engagement->getSubject())
        ->toBe('Q&A session: "What\'s next?"');
});

it('resolves merge tags in the subject', function () {
    $student = Student::factory()->create([
        'first' => 'Jane',
    ]);

    $engagement = Engagement::factory()->email()->create([
        'recipient_id' => $student->getKey(),
        'recipient_type' => $student->getMorphClass(),
        'subject' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Hello '),
                tiptapMergeTag('recipient first name'),
            ]),
        ]),
    ]);

    expect((string) $engagement->getSubject())
        ->toBe('Hello Jane');
});

it('resolves merge tags in the body text', function () {
    $student = Student::factory()->create([
        'full_name' => 'Jane Doe',
    ]);

    $engagement = Engagement::factory()->sms()->create([
        'recipient_id' => $student->getKey(),
        'recipient_type' => $student->getMorphClass(),
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Dear '),
                tiptapMergeTag('recipient full name'),
                tiptapText(', your appointment is confirmed.'),
            ]),
        ]),
    ]);

    expect($engagement->getBodyText())
        ->toBe('Dear Jane Doe , your appointment is confirmed.');
});

it('resolves merge tags in the body html', function () {
    $student = Student::factory()->create([
        'full_name' => 'Jane Doe',
    ]);

    $engagement = Engagement::factory()->email()->create([
        'recipient_id' => $student->getKey(),
        'recipient_type' => $student->getMorphClass(),
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Dear '),
                tiptapMergeTag('recipient full name'),
                tiptapText(', welcome!'),
            ]),
        ]),
    ]);

    expect((string) $engagement->getBody())
        ->toBe('<p>Dear <span>Jane Doe</span>, welcome!</p>');
});

it('resolves user merge tags', function () {
    $user = User::factory()->create([
        'name' => 'John Smith',
        'email' => 'john@example.com',
    ]);

    $engagement = Engagement::factory()->forStudent()->sms()->create([
        'user_id' => $user->id,
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('From: '),
                tiptapMergeTag('user full name'),
                tiptapText(' ('),
                tiptapMergeTag('user email'),
                tiptapText(')'),
            ]),
        ]),
    ]);

    expect($engagement->getBodyText())
        ->toBe('From: John Smith ( john@example.com )');
});

it('returns body markdown', function () {
    $engagement = Engagement::factory()->forStudent()->email()->create([
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Hello world'),
            ]),
        ]),
    ]);

    expect($engagement->getBodyMarkdown())
        ->toBe('Hello world');
});

it('returns subject markdown', function () {
    $engagement = Engagement::factory()->forStudent()->email()->create([
        'subject' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Test subject'),
            ]),
        ]),
    ]);

    expect($engagement->getSubjectMarkdown())
        ->toBe('Test subject');
});

it('returns null for subject markdown when subject is empty', function () {
    $engagement = Engagement::factory()->forStudent()->email()->create([
        'subject' => tiptapDoc([
            tiptapParagraph([]),
        ]),
    ]);

    expect($engagement->getSubjectMarkdown())->toBeNull();
});

it('collapses whitespace in subject', function () {
    $engagement = Engagement::factory()->forStudent()->email()->create([
        'subject' => tiptapDoc([
            tiptapParagraph([
                tiptapText('Hello   world'),
            ]),
        ]),
    ]);

    expect((string) $engagement->getSubject())
        ->toBe('Hello world');
});

it('collapses multiple paragraphs in body text', function () {
    $engagement = Engagement::factory()->forStudent()->sms()->create([
        'body' => tiptapDoc([
            tiptapParagraph([
                tiptapText('First paragraph.'),
            ]),
            tiptapParagraph([
                tiptapText('Second paragraph.'),
            ]),
        ]),
    ]);

    expect($engagement->getBodyText())
        ->toBe('First paragraph. Second paragraph.');
});
