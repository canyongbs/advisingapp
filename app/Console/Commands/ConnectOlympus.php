<?php

namespace App\Console\Commands;

use App\Settings\BrandSettings;
use App\Settings\OlympusSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ConnectOlympus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olympus:connect {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect the app to communicate with Olympus.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $response = Http::post($this->argument('url'), [
            'url' => config('app.url'),
        ])->throw();

        $olympusSettings = app(OlympusSettings::class);
        $olympusSettings->fill($response->json('olympus'));
        $olympusSettings->save();

        $brandSettings = app(BrandSettings::class);
        $brandSettings->fill($response->json('brand'));
        $brandSettings->save();

        $this->info('The app has been connected to Olympus.');
    }
}
