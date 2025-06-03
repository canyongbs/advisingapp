<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AllUsersController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $users = [];

        Tenant::query()
            ->where('setup_complete', true)
            ->get()
            ->eachCurrent(function (Tenant $tenant) use (&$users) {
                User::all()
                    ->each(function (User $user) use (&$users, $tenant) {
                        $users[] = [
                            'id' => $user->id,
                            'tenant_id' => $tenant->getKey(),
                            'name' => $user->name,
                            'job_title' => $user->job_title,
                            'team' => $user->team->name ?? null,
                            'email' => $user->email,
                            'timezone' => $user->timezone,
                            'first_login_at' => $user->first_login_at,
                            'last_logged_in_at' => $user->last_logged_in_at,
                            'updated_at' => $user->updated_at,
                            'created_at' => $user->created_at,
                        ];
                    });
            });

        return response()->json([
            'data' => $users,
            'meta' => [
                'total_users' => count($users),
            ],
        ]);
    }
}
