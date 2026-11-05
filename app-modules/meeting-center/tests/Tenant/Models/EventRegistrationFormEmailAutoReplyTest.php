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

use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormEmailAutoReply;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$richText = fn (array $content): array => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => $content]]];

it('fills the `recipient email` merge tag from the attendee', function () use ($richText): void {
    $attendee = EventAttendee::factory()->make(['event_id' => null, 'email' => 'jamie@example.com']);

    $emailAutoReply = EventRegistrationFormEmailAutoReply::factory()->make([
        'form_id' => null,
        'subject' => $richText([
            ['type' => 'text', 'text' => 'Registered as '],
            ['type' => 'mergeTag', 'attrs' => ['id' => 'recipient email']],
        ]),
        'body' => $richText([
            ['type' => 'text', 'text' => 'We will email '],
            ['type' => 'mergeTag', 'attrs' => ['id' => 'recipient email']],
        ]),
    ]);

    expect($emailAutoReply->getSubject($attendee))->toBe('Registered as jamie@example.com')
        ->and(html_entity_decode($emailAutoReply->getBody($attendee)))->toContain('jamie@example.com');
});

it('leaves the `recipient email` merge tag empty when there is no attendee', function () use ($richText): void {
    $emailAutoReply = EventRegistrationFormEmailAutoReply::factory()->make([
        'form_id' => null,
        'subject' => $richText([
            ['type' => 'text', 'text' => 'Registered as '],
            ['type' => 'mergeTag', 'attrs' => ['id' => 'recipient email']],
        ]),
        'body' => $richText([
            ['type' => 'text', 'text' => 'See you there'],
            ['type' => 'mergeTag', 'attrs' => ['id' => 'recipient email']],
        ]),
    ]);

    expect($emailAutoReply->getSubject(null))->toBe('Registered as')
        ->and($emailAutoReply->getBody(null))->toContain('See you there');
});

it('returns the subject as plain text', function () use ($richText): void {
    $emailAutoReply = EventRegistrationFormEmailAutoReply::factory()->make([
        'form_id' => null,
        'subject' => $richText([
            ['type' => 'text', 'text' => 'Fish & Chips', 'marks' => [['type' => 'bold']]],
            ['type' => 'text', 'text' => "   extra \n  spaces"],
        ]),
    ]);

    expect($emailAutoReply->getSubject(null))->toBe('Fish & Chips extra spaces');
});

it('returns empty strings when the subject and body are empty', function (): void {
    $emailAutoReply = EventRegistrationFormEmailAutoReply::factory()->make([
        'form_id' => null,
        'subject' => null,
        'body' => null,
    ]);

    expect($emailAutoReply->getSubject(null))->toBe('')
        ->and($emailAutoReply->getBody(null))->toBe('');
});

it('renders body images with permanent urls so they keep working in sent emails', function (): void {
    Storage::fake('s3-public');
    Storage::disk('s3-public')->buildTemporaryUrlsUsing(fn (string $path): string => "https://temporary.example.com/{$path}");

    $emailAutoReply = Event::factory()->create()->eventRegistrationForm->emailAutoReply;
    $image = $emailAutoReply->addMedia(UploadedFile::fake()->image('logo.png'))->toMediaCollection('body', 's3-public');
    $emailAutoReply->update([
        'body' => ['type' => 'doc', 'content' => [['type' => 'image', 'attrs' => ['id' => $image->uuid]]]],
    ]);

    expect($emailAutoReply->refresh()->getBody(null))
        ->toContain($image->getUrl())
        ->not->toContain('temporary.example.com');
});
