<?php

use AdvisingApp\Authorization\Models\Role;
use App\Models\Authenticatable;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Role::create([
            'name' => Authenticatable::PARTNER_ADMIN_ROLE,
            'guard_name' => 'web',
        ]);
    }

    public function down(): void
    {
        Role::query()
            ->where('name', Authenticatable::PARTNER_ADMIN_ROLE)
            ->where('guard_name', 'web')
            ->delete();
    }
};
