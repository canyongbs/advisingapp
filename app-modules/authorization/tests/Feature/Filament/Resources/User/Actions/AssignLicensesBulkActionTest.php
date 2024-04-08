<?php

use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Collection;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertFalse;

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Filament\Resources\UserResource\Actions\AssignLicensesBulkAction;

/**
 * @param User|null $user
 * @param User|Collection<User>|null $records
 * @param LicenseType|array<LicenseType>|null $licenseTypes
 *
 * @return array{user: User, records: EloquentCollection<User>, licenseTypes: Collection<LicenseType>}
 */
$setUp = function (
    User $user = null,
    User | Collection $records = null,
    LicenseType | array $licenseTypes = null,
) {
    $user ??= User::factory()->create();
    $user->assignRole([
        'authorization.user_management',
        'authorization.license_management',
    ]);
    actingAs($user);

    $records ??= User::factory(2)->create();
    $records->prepend($user);

    $licenseTypes = collect($licenseTypes ?? LicenseType::cases());

    $records->each(function (User $record) use ($licenseTypes) {
        $licenseTypes->each(fn ($licenseType) => assertFalse($record->hasLicense($licenseType)));
    });

    return ['user' => $user, 'records' => $records, 'licenseTypes' => $licenseTypes];
};

it('will assign licenses to users', function ($licenseType) use ($setUp) {
    ['records' => $records, 'licenseTypes' => $licenseTypes] = $setUp(licenseTypes: $licenseType);

    livewire(ListUsers::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->callTableBulkAction(AssignLicensesBulkAction::class, $records, [
            'replace' => false,
            ...$licenseTypes->mapWithKeys(fn (LicenseType $licenseType) => [$licenseType->value => true]),
        ])
        ->assertHasNoTableBulkActionErrors()
        ->assertNotified();

    $records->each(function (User $record) use ($licenseTypes) {
        $record->refresh();
        $licenseTypes->each(fn (LicenseType $licenseType) => assertTrue($record->hasLicense($licenseType)));
    });
})->with(LicenseType::cases());

it('will replace existing licenses', function () use ($setUp) {
    ['records' => $records, 'licenseTypes' => $licenseTypes] = $setUp();

    [$add, $existing] = $licenseTypes->split(2);

    $records->each(function (User $record) use ($existing) {
        $existing->each(function (LicenseType $licenseType) use ($record) {
            $record->grantLicense($licenseType);
            assertTrue($record->refresh()->hasLicense($licenseType));
        });
    });

    livewire(ListUsers::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->callTableBulkAction(AssignLicensesBulkAction::class, $records, [
            'replace' => true,
            ...$add->mapWithKeys(fn (LicenseType $licenseType) => [$licenseType->value => true]),
        ])
        ->assertHasNoTableBulkActionErrors()
        ->assertNotified();

    $records->each(function (User $record) use ($add, $existing) {
        $record->refresh();
        $add->each(fn (LicenseType $licenseType) => assertTrue($record->hasLicense($licenseType)));
        $existing->each(fn (LicenseType $licenseType) => assertFalse($record->hasLicense($licenseType)));
    });
});

it('it will not revoke existing licenses if not replacing', function () use ($setUp) {
    ['records' => $records, 'licenseTypes' => $licenseTypes] = $setUp();

    [$add, $existing] = $licenseTypes->split(2);

    $records->each(function (User $record) use ($existing) {
        $existing->each(function (LicenseType $licenseType) use ($record) {
            $record->grantLicense($licenseType);
            assertTrue($record->refresh()->hasLicense($licenseType));
        });
    });

    livewire(ListUsers::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->callTableBulkAction(AssignLicensesBulkAction::class, $records, [
            'replace' => false,
            ...$add->mapWithKeys(fn (LicenseType $licenseType) => [$licenseType->value => true]),
        ])
        ->assertHasNoTableBulkActionErrors()
        ->assertNotified();

    $records->each(function (User $record) use ($add, $existing) {
        $record->refresh();
        $add->each(fn (LicenseType $licenseType) => assertTrue($record->hasLicense($licenseType)));
        $existing->each(fn (LicenseType $licenseType) => assertTrue($record->hasLicense($licenseType)));
    });
});

it('will revoke licenses if replacing', function () use ($setUp) {
    ['records' => $records, 'licenseTypes' => $licenseTypes] = $setUp();

    $records->each(function (User $record) use ($licenseTypes) {
        $licenseTypes->each(function (LicenseType $licenseType) use ($record) {
            $record->grantLicense($licenseType);
            assertTrue($record->refresh()->hasLicense($licenseType));
        });
    });

    livewire(ListUsers::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->callTableBulkAction(AssignLicensesBulkAction::class, $records, [
            'replace' => true,
            ...$licenseTypes->mapWithKeys(fn (LicenseType $licenseType) => [$licenseType->value => false]),
        ])
        ->assertHasNoTableBulkActionErrors()
        ->assertNotified();

    $records->each(function (User $record) use ($licenseTypes) {
        $record->refresh();
        $licenseTypes->each(fn (LicenseType $licenseType) => assertFalse($record->hasLicense($licenseType)));
    });
});

it('will not allow assigning more licenses than available', function () use ($setUp) {
    ['records' => $records, 'licenseTypes' => $licenseTypes] = $setUp();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->conversationalAiSeats = 1;
    $licenseSettings->data->limits->retentionCrmSeats = 1;
    $licenseSettings->data->limits->recruitmentCrmSeats = 1;
    $licenseSettings->save();

    livewire(ListUsers::class)
        ->assertSuccessful()
        ->assertCountTableRecords($records->count())
        ->callTableBulkAction(AssignLicensesBulkAction::class, $records, [
            'replace' => false,
            ...$licenseTypes->mapWithKeys(fn (LicenseType $licenseType) => [$licenseType->value => true]),
        ])
        ->assertHasTableBulkActionErrors([
            'conversational_ai' => ['You do not have enough seats for ' . LicenseType::ConversationalAi->getLabel()],
            'retention_crm' => ['You do not have enough seats for ' . LicenseType::RetentionCrm->getLabel()],
            'recruitment_crm' => ['You do not have enough seats for ' . LicenseType::RecruitmentCrm->getLabel()],
        ])
        ->assertNotNotified();
});
