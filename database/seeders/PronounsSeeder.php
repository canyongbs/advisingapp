<?php

namespace Database\Seeders;

use App\Models\Pronouns;
use Illuminate\Database\Seeder;

class PronounsSeeder extends Seeder
{
    public function run(): void
    {
        Pronouns::factory()
            ->createMany(
                [
                    [
                        'label' => 'She/Her',
                    ],
                    [
                        'label' => 'He/Him',
                    ],
                    [
                        'label' => 'They/Them',
                    ],
                ],
            );
    }
}
