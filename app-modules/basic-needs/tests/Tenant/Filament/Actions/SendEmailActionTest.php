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

use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsPrograms\Pages\ViewBasicNeedsProgram;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Forms\Components\Select;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function assertStudentRecipientSelect(Closure $assertUsing): void
{
    livewire(ViewBasicNeedsProgram::class, ['record' => BasicNeedsProgram::factory()->create()->getKey()])
        ->mountAction('send_email')
        ->fillForm(['recipient_type' => 'student'])
        ->assertSchemaComponentExists('recipient_id', checkComponentUsing: function (Select $field) use ($assertUsing): bool {
            $assertUsing($field);

            return true;
        });
}

/**
 * @param array<array-key, string> $options
 *
 * @return array<string>
 */
function recipientSisids(array $options): array
{
    // Numeric-string SIS IDs come back as integer array keys.
    return array_map(strval(...), array_keys($options));
}

it('offers active students as recipients', function () {
    asSuperAdmin();

    $student = Student::factory()->create(['full_name' => 'Recipient Alpha']);

    assertStudentRecipientSelect(function (Select $field) use ($student): void {
        expect(recipientSisids($field->getOptions()))->toContain($student->getKey())
            ->and(recipientSisids($field->getSearchResults('recipient alpha')))->toContain($student->getKey());
    });
});

it('does not offer archived students as recipients', function () {
    asSuperAdmin();

    $student = Student::factory()->create(['full_name' => 'Recipient Beta']);
    $student->archive();

    assertStudentRecipientSelect(function (Select $field) use ($student): void {
        expect(recipientSisids($field->getOptions()))->not->toContain($student->getKey())
            ->and(recipientSisids($field->getSearchResults('recipient beta')))->not->toContain($student->getKey());
    });
});

it('does not offer an archived student searched by their SIS ID or email address', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    $address = $student->primaryEmailAddress->address;
    $student->archive();

    assertStudentRecipientSelect(function (Select $field) use ($student, $address): void {
        expect(recipientSisids($field->getSearchResults($student->sisid)))->not->toContain($student->getKey())
            ->and(recipientSisids($field->getSearchResults($address)))->not->toContain($student->getKey());
    });
});
