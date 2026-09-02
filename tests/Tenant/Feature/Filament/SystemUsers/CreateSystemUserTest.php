<?php

use App\Filament\Resources\SystemUsers\Pages\CreateSystemUser;
use App\Models\SystemUser;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('CreateSystemUser does not allow for duplicate names of non-deleted system users case insensitively', function () {
    asSuperAdmin();

    $systemUser = SystemUser::factory(['name' => 'System User'])->create();
    $systemUser->delete();

    livewire(CreateSystemUser::class)
        ->fillForm(['name' => 'system USER'])
        ->call('create')
        ->assertHasNoActionErrors();

    livewire(CreateSystemUser::class)
        ->fillForm(['name' => 'system user'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});