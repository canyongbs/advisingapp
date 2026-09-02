<?php

use App\Filament\Resources\SystemUsers\Pages\EditSystemUser;
use App\Models\SystemUser;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditSystemUser does not allow for duplicate names of non-deleted system users case insensitively', function () {
    asSuperAdmin();

    $deletedSystemUser = SystemUser::factory(['name' => 'System User'])->create();
    $systemUser = SystemUser::factory(['name' => 'Test System User'])->create();
    SystemUser::factory(['name' => 'Other System User'])->create();

    $deletedSystemUser->delete();

    livewire(EditSystemUser::class, ['record' => $systemUser->getRouteKey()])
        ->fillForm(['name' => 'system user'])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(EditSystemUser::class, ['record' => $systemUser->getRouteKey()])
        ->fillForm(['name' => 'OTHER System user'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});