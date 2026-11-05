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

use AdvisingApp\Engagement\Filament\Resources\SmsTemplates\Pages\EditSmsTemplate;
use AdvisingApp\Engagement\Models\SmsTemplate;
use App\Features\TemplateNameCitextFeature;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

beforeEach(function (): void {
    asSuperAdmin();
});

it('does not allow renaming a sms template to a name matching another non-deleted template case-insensitively', function (): void {
    SmsTemplate::factory()->create(['name' => 'Welcome Message']);
    $template = SmsTemplate::factory()->create(['name' => 'Goodbye Message']);

    Livewire::test(EditSmsTemplate::class, ['record' => $template->getRouteKey()])
        ->fillForm(['name' => 'welcome message'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('allows saving a sms template without changing its name', function (): void {
    $template = SmsTemplate::factory()->create(['name' => 'Welcome Message']);

    Livewire::test(EditSmsTemplate::class, ['record' => $template->getRouteKey()])
        ->fillForm(['name' => 'Welcome Message'])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('allows renaming a sms template to a name freed up by a soft-deleted template case-insensitively', function (): void {
    SmsTemplate::factory()->create(['name' => 'Welcome Message'])->delete();
    $template = SmsTemplate::factory()->create(['name' => 'Goodbye Message']);

    Livewire::test(EditSmsTemplate::class, ['record' => $template->getRouteKey()])
        ->fillForm(['name' => 'welcome message'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($template->refresh()->name)->toBe('welcome message');
});

it('allows editing a sms template that has an exact duplicate name before the citext migration activates', function (): void {
    TemplateNameCitextFeature::deactivate();

    DB::statement('DROP INDEX IF EXISTS sms_templates_name_unique');

    SmsTemplate::factory()->create(['name' => 'Welcome Message']);
    $template = SmsTemplate::factory()->create(['name' => 'Welcome Message']);

    Livewire::test(EditSmsTemplate::class, ['record' => $template->getRouteKey()])
        ->fillForm(['description' => 'Updated description'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($template->refresh()->description)->toBe('Updated description');
});
