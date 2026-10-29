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

use AdvisingApp\Engagement\Filament\Resources\EmailTemplates\Pages\CreateEmailTemplate;
use AdvisingApp\Engagement\Models\EmailTemplate;
use App\Features\TemplateNameCitextFeature;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    asSuperAdmin();
});

it('does not allow creating an email template whose name matches another non-deleted template case-insensitively', function (): void {
    EmailTemplate::factory()->create(['name' => 'Welcome Message']);

    Livewire::test(CreateEmailTemplate::class)
        ->fillForm(['name' => 'welcome message', 'content' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Hello there']]]]]])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('allows creating an email template whose name is freed up by a soft-deleted template case-insensitively', function (): void {
    EmailTemplate::factory()->create(['name' => 'Welcome Message'])->delete();

    Livewire::test(CreateEmailTemplate::class)
        ->fillForm(['name' => 'welcome message', 'content' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Hello there']]]]]])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(EmailTemplate::class, ['name' => 'welcome message', 'deleted_at' => null]);
});

it('does not apply name uniqueness validation before the citext migration activates', function (): void {
    TemplateNameCitextFeature::deactivate();

    DB::statement('DROP INDEX IF EXISTS email_templates_name_unique');

    EmailTemplate::factory()->create(['name' => 'Welcome Message']);

    Livewire::test(CreateEmailTemplate::class)
        ->fillForm(['name' => 'Welcome Message', 'content' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Hello there']]]]]])
        ->call('create')
        ->assertHasNoFormErrors();
});
