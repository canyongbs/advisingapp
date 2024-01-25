<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Console\ConfirmableTrait;

class GenerateLandlordApiKey extends Command
{
    use ConfirmableTrait;

    protected $signature = 'app:generate-landlord-api-key';

    protected $description = 'Generate a new API key for the landlord';

    public function handle(): int
    {
        $plaintextKey = base64_encode(uniqid('landlord-api-key-', true));

        $hash = base64_encode(Hash::make($plaintextKey));

        if ($this->setKeyInEnvironmentFile($hash)) {
            $this->info('The plaintext API key is: ' . $plaintextKey);

            return self::SUCCESS;
        }

        $this->error('API key set failed.');

        return self::FAILURE;
    }

    protected function setKeyInEnvironmentFile($key): bool
    {
        $currentKey = $this->laravel['config']['app.key'];

        if (strlen($currentKey) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        return $this->writeNewEnvironmentFileWith($key);
    }

    protected function writeNewEnvironmentFileWith($key): bool
    {
        $replaced = preg_replace(
            $this->keyReplacementPattern(),
            'LANDLORD_API_KEY=' . $key,
            $input = file_get_contents($this->laravel->environmentFilePath())
        );

        if ($replaced === $input || $replaced === null) {
            $this->error('Unable to set application key. No LANDLORD_API_KEY variable was found in the .env file.');

            return false;
        }

        file_put_contents($this->laravel->environmentFilePath(), $replaced);

        return true;
    }

    protected function keyReplacementPattern(): string
    {
        $escaped = preg_quote('=' . $this->laravel['config']['app.landlord_api_key'], '/');

        return "/^LANDLORD_API_KEY{$escaped}/m";
    }
}
