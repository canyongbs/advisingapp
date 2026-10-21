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

use AdvisingApp\Form\Actions\ProcessSubmissionField;
use AdvisingApp\Form\Events\FormSubmissionCreated;
use AdvisingApp\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormAuthentication;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Form\Notifications\AuthorLinkedFormSubmissionCreatedNotification;
use AdvisingApp\Form\Notifications\FormSubmissionAutoReplyNotification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use App\Settings\LicenseSettings;
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Mockery\MockInterface;

use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withoutMiddleware;
use function Tests\asSuperAdmin;

beforeEach(function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->onlineForms = true;

    $settings->save();
});

test('view endpoint returns an empty schema for an authenticated form', function () {
    // This is the constraint that forces the preview to use a dedicated endpoint:
    // the public entry endpoint must not leak the schema before authentication.
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    $form = Form::factory()->create(['is_authenticated' => true]);

    $response = get(route('widgets.forms.api.entry', ['form' => $form]))
        ->assertSuccessful();

    expect($response->json('is_authenticated'))->toBeTrue();
    expect($response->json('schema'))->toBe([]);
});

test('preview endpoint returns a populated schema for an authenticated form', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);

    expect($form->fields()->count())->toBeGreaterThan(0);

    $response = get(route('forms.api.preview', ['form' => $form]))
        ->assertSuccessful();

    expect($response->json('is_authenticated'))->toBeTrue();
    expect($response->json('schema.children'))->toBeArray()->not->toBeEmpty();
});

test('preview renders the form fields for an authenticated form', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);

    $form->fields()->delete();

    $firstName = $form->fields()->create([
        'label' => 'What is your first name?',
        'type' => 'text_input',
        'is_required' => true,
        'config' => [],
    ]);

    $aboutYou = $form->fields()->create([
        'label' => 'Tell us about yourself',
        'type' => 'text_area',
        'is_required' => false,
        'config' => [],
    ]);

    $block = fn (FormField $field): array => [
        'type' => 'customBlock',
        'attrs' => [
            'config' => [
                'fieldId' => $field->id,
                'label' => $field->label,
                'isRequired' => $field->is_required,
            ],
            'id' => $field->type,
        ],
    ];

    $form->content = [
        'type' => 'doc',
        'content' => [$block($firstName), $block($aboutYou)],
    ];

    $form->save();

    get(route('forms.api.preview', ['form' => $form]))
        ->assertSuccessful()
        ->assertJsonFragment(['label' => 'What is your first name?'])
        ->assertJsonFragment(['label' => 'Tell us about yourself']);
});

test('authenticate locks out after too many invalid code attempts', function () {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    $form = Form::factory()->create();

    $prospect = Prospect::factory()->create();

    $code = '123456';

    $authentication = new FormAuthentication();
    $authentication->author()->associate($prospect);
    $authentication->submissible()->associate($form);
    $authentication->code = Hash::make($code);
    $authentication->save();

    $invalidUrl = URL::signedRoute(
        name: 'widgets.forms.api.authenticate',
        parameters: ['form' => $form, 'authentication' => $authentication, 'code' => '654321'],
    );

    foreach (range(1, AuthenticationCodeRateLimiter::MAX_ATTEMPTS) as $attempt) {
        postJson($invalidUrl)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code' => 'The provided code is invalid.']);
    }

    postJson(URL::signedRoute(
        name: 'widgets.forms.api.authenticate',
        parameters: ['form' => $form, 'authentication' => $authentication, 'code' => $code],
    ))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code' => 'Too many invalid attempts. Please request a new code.']);
});

$createRequestedSubmissionWithAutoReplyAndFollower = function (): array {
    $form = Form::factory()->create(['is_authenticated' => true]);
    $form->fields()->delete();

    $field = $form->fields()->create([
        'label' => 'Tell us about yourself',
        'type' => 'text_input',
        'is_required' => false,
        'config' => [],
    ]);

    $richText = fn (string $text): array => [
        'type' => 'doc',
        'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]]],
    ];

    $form->emailAutoReply->update([
        'is_enabled' => true,
        'subject' => $richText('Thanks for submitting'),
        'body' => $richText('We received your submission.'),
    ]);

    $student = Student::factory()->create();

    $follower = User::factory()->create();
    $follower->subscriptions()->create([
        'subscribable_id' => $student->getKey(),
        'subscribable_type' => $student->getMorphClass(),
    ]);

    $requestedSubmission = FormSubmission::factory()->create([
        'form_id' => $form->getKey(),
        'author_type' => $student->getMorphClass(),
        'author_id' => $student->getKey(),
        'submitted_at' => null,
    ]);

    $authentication = new FormAuthentication();
    $authentication->author()->associate($student);
    $authentication->submissible()->associate($form);
    $authentication->code = Hash::make('123456');
    $authentication->save();

    return [$form, $field, $student, $follower, $requestedSubmission, $authentication];
};

it('completes a requested submission and dispatches `FormSubmissionCreated` once when it is submitted', function () use ($createRequestedSubmissionWithAutoReplyAndFollower) {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    Queue::fake();

    [$form, , , , $requestedSubmission, $authentication] = $createRequestedSubmissionWithAutoReplyAndFollower();

    expect($requestedSubmission->submitted_at)->toBeNull();

    Event::fake(FormSubmissionCreated::class);

    postJson(URL::signedRoute(
        name: 'widgets.forms.api.submit',
        parameters: ['form' => $form, 'authentication' => $authentication],
    ))
        ->assertSuccessful()
        ->assertJson(['message' => 'Form submitted successfully.']);

    expect($form->submissions()->count())->toBe(1)
        ->and($requestedSubmission->refresh()->submitted_at)->not->toBeNull();

    Event::assertDispatchedTimes(FormSubmissionCreated::class, 1);
    Event::assertDispatched(
        event: FormSubmissionCreated::class,
        callback: fn (FormSubmissionCreated $event) => $event->submission->is($requestedSubmission)
    );
});

it('sends the email auto reply and notifies followers once when a requested submission is submitted', function () use ($createRequestedSubmissionWithAutoReplyAndFollower) {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    Notification::fake();

    [$form, $field, $student, $follower, $requestedSubmission, $authentication] = $createRequestedSubmissionWithAutoReplyAndFollower();

    Notification::assertNotSentTo($student, FormSubmissionAutoReplyNotification::class);

    postJson(URL::signedRoute(
        name: 'widgets.forms.api.submit',
        parameters: ['form' => $form, 'authentication' => $authentication],
    ), [$field->getKey() => 'Hello'])
        ->assertSuccessful();

    expect($requestedSubmission->refresh()->submitted_at)->not->toBeNull();

    Notification::assertSentToTimes($student, FormSubmissionAutoReplyNotification::class, 1);
    Notification::assertSentToTimes($follower, AuthorLinkedFormSubmissionCreatedNotification::class, 1);
});

it('does not send the email auto reply or notify followers when a requested submission fails and is rolled back', function () use ($createRequestedSubmissionWithAutoReplyAndFollower) {
    withoutMiddleware([EnsureSubmissibleIsEmbeddableAndAuthorized::class]);

    Notification::fake();
    Exceptions::fake();

    [$form, $field, $student, $follower, $requestedSubmission, $authentication] = $createRequestedSubmissionWithAutoReplyAndFollower();

    mock(ProcessSubmissionField::class, function (MockInterface $mock) {
        $mock->shouldReceive('__invoke')->andThrow(new Exception('Field processing failed.'));
    });

    postJson(URL::signedRoute(
        name: 'widgets.forms.api.submit',
        parameters: ['form' => $form, 'authentication' => $authentication],
    ), [$field->getKey() => 'Hello'])
        ->assertInternalServerError();

    Exceptions::assertReported(fn (Exception $exception): bool => $exception->getMessage() === 'Field processing failed.');

    expect($requestedSubmission->refresh()->submitted_at)->toBeNull()
        ->and(FormAuthentication::query()->whereKey($authentication->getKey())->exists())->toBeTrue();

    Notification::assertNotSentTo($student, FormSubmissionAutoReplyNotification::class);
    Notification::assertNotSentTo($follower, AuthorLinkedFormSubmissionCreatedNotification::class);
});
