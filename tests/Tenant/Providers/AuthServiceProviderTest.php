<?php

use AdvisingApp\Authorization\Models\Role;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\{actingAs, get};

describe('API documentation authorization', function (): void {
    it('allows super admins to access the API documentation', function (): void {
        $user = User::factory()->create();
        $role = Role::query()->firstOrCreate([
            'name' => Authenticatable::SUPER_ADMIN_ROLE,
            'guard_name' => 'web',
        ]);
        $user->assignRole($role);

        expect(Gate::forUser($user)->allows('viewApiDocs'))->toBeTrue();

        actingAs($user);

        get('/docs/api/v1')
            ->assertSuccessful();

        get('/docs/api/v1.json')->assertSuccessful();
    });

    it('allows partner admins to access the API documentation', function (): void {
        $user = User::factory()->create();
        $role = Role::query()->firstOrCreate([
            'name' => Authenticatable::PARTNER_ADMIN_ROLE,
            'guard_name' => 'web',
        ]);
        $user->assignRole($role);

        expect(Gate::forUser($user)->allows('viewApiDocs'))->toBeTrue();

        actingAs($user);

        get('/docs/api/v1')
            ->assertSuccessful();

        get('/docs/api/v1.json')->assertSuccessful();
    });

    it('denies API documentation access to regular users and guests', function (): void {
        $user = User::factory()->create();

        expect(Gate::forUser($user)->allows('viewApiDocs'))->toBeFalse()
            ->and(Gate::allows('viewApiDocs'))->toBeFalse();

        actingAs($user);

        get('/docs/api/v1')
            ->assertForbidden();

        get('/docs/api/v1.json')->assertForbidden();

        auth()->logout();

        get('/docs/api/v1')
            ->assertForbidden();

        get('/docs/api/v1.json')->assertForbidden();
    });
});
