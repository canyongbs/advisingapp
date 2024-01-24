<?php

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

        $this->setKeyInEnvironmentFile($hash);

        $this->info('The plaintext API key is: ' . $plaintextKey);

        return 0;
    }

    protected function setKeyInEnvironmentFile($key): bool
    {
        $currentKey = $this->laravel['config']['app.key'];

        if (strlen($currentKey) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        if (! $this->writeNewEnvironmentFileWith($key)) {
            return false;
        }

        return true;
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
