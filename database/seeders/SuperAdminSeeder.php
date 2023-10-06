<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Assist\Prospect\Models\Prospect;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Models\EngagementDeliverable;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        /** Super Admin */
        $superAdmin = User::where('email', 'superadmin@assist.com')->first();

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

        // Prospect subscriptions
        Prospect::query()
            ->orderBy('id')
            ->limit(25)
            ->get()
            ->each(function (Prospect $prospect) use ($user) {
                $user->subscriptions()->create([
                    'subscribable_id' => $prospect->id,
                    'subscribable_type' => resolve(Prospect::class)->getMorphClass(),
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
                $numberOfEngagements = rand(1, 10);

                for ($i = 0; $i < $numberOfEngagements; $i++) {
                    Engagement::factory()
                        ->has(EngagementDeliverable::factory()->count(1)->randomizeState(), 'engagementDeliverables')
                        ->for($student, 'recipient')
                        ->create([
                            'user_id' => $user->id,
                        ]);
                }

                EngagementResponse::factory()
                    ->count(rand(1, 10))
                    ->for($student, 'sender')
                    ->create();
            });

        // Prospect Engagements
        Prospect::query()
            ->orderBy('id')
            ->limit(25)
            ->get()
            ->each(function (Prospect $prospect) use ($user) {
                $numberOfEngagements = rand(1, 10);

                for ($i = 0; $i < $numberOfEngagements; $i++) {
                    Engagement::factory()
                        ->has(EngagementDeliverable::factory()->count(1)->randomizeState(), 'engagementDeliverables')
                        ->for($prospect, 'recipient')
                        ->create([
                            'user_id' => $user->id,
                        ]);
                }

                EngagementResponse::factory()
                    ->count(rand(1, 10))
                    ->for($prospect, 'sender')
                    ->create();
            });
    }
}
