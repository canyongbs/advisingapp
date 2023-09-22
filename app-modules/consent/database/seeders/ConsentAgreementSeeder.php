<?php

namespace Assist\Consent\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Enums\ConsentAgreementType;

class ConsentAgreementSeeder extends Seeder
{
    public function run(): void
    {
        // Azure OpenAI Consent Agreement
        ConsentAgreement::factory()
            ->create([
                'title' => 'Azure OpenAI Consent Agreement',
                'description' => 'Please confirm that you have read the following agreement and consent to the terms and conditions.',
                'type' => ConsentAgreementType::AzureOpenAI,
            ]);
    }
}
