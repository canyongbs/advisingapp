<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Assist\Authorization\Models\Role;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Models\EngagementDeliverable;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        /** Super Admin */
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@assist.com',
            'password' => Hash::make('password'),
        ]);

        $superAdminRoles = Role::superAdmin()->get();

        $superAdmin->assignRole($superAdminRoles);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@assist.com',
            'password' => Hash::make('password'),
        ]);

        // Data for super admin
        $this->seedSubscribersFor($superAdmin);
        $this->seedEngagementsFor($superAdmin);
    }

    protected function seedSubscribersFor(User $user): void
    {
        // Student subscriptions
        Student::query()
            ->orderBy('sisid')
            ->limit(25)
            ->get()
            ->each(function (Student $student) use ($user) {
                $user->subscriptions()->create([
                    'subscribable_id' => $student->sisid,
                    'subscribable_type' => resolve(Student::class)->getMorphClass(),
                ]);
            });
    }

    protected function seedEngagementsFor(User $user): void
    {
        // Student Engagements
        Student::query()
            ->orderBy('sisid')
            ->limit(25)
            ->get()
            ->each(function (Student $student) use ($user) {
                Engagement::factory()
                    ->count(rand(1, 10))
                    ->has(EngagementDeliverable::factory()->count(1), 'engagementDeliverables')
                    ->for($student, 'recipient')
                    ->create([
                        'user_id' => $user->id,
                    ]);

                EngagementResponse::factory()
                    ->count(rand(1, 10))
                    ->for($student, 'sender')
                    ->create();
            });
    }
}
