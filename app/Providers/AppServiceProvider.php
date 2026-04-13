<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace App\Providers;

use AdvisingApp\Campaign\Jobs\EngagementCampaignActionJob;
use AdvisingApp\Engagement\Jobs\CreateBatchedEngagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Pipeline\Models\Pipeline;
use AdvisingApp\Pipeline\Models\PipelineStage;
use AdvisingApp\Workflow\Jobs\EngagementEmailWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\EngagementSmsWorkflowActionJob;
use App\Models\SystemUser;
use App\Models\Tenant;
use App\Notifications\ResetPasswordNotification;
use App\Overrides\Filament\Actions\Exports\Jobs\CreateXlsxFileOverride;
use App\Overrides\Filament\Actions\Exports\Jobs\ExportCompletionOverride;
use App\Overrides\Filament\Actions\Exports\Jobs\ExportCsvOverride;
use App\Overrides\Filament\Actions\Exports\Jobs\PrepareCsvExportOverride;
use App\Overrides\Filament\Actions\Imports\Jobs\ImportCsvOverride;
use App\Overrides\Laravel\PermissionMigrationCreator;
use App\Overrides\Laravel\StartSession as OverrideStartSession;
use Aws\GeoPlaces\GeoPlacesClient;
use Exception;
use Filament\Actions\Exports\Jobs\CreateXlsxFile;
use Filament\Actions\Exports\Jobs\ExportCompletion;
use Filament\Actions\Exports\Jobs\ExportCsv;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Commands\ReloadCommand;
use Laravel\Pennant\Feature;
use Rector\Caching\CacheFactory;

use function Sentry\configureScope;

use Sentry\State\Scope;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ImportCsv::class, ImportCsvOverride::class);
        $this->app->bind(PrepareCsvExport::class, PrepareCsvExportOverride::class);
        $this->app->bind(ExportCsv::class, ExportCsvOverride::class);
        $this->app->bind(ExportCompletion::class, ExportCompletionOverride::class);
        $this->app->bind(CreateXlsxFile::class, CreateXlsxFileOverride::class);
        $this->app->bind(ResetPassword::class, ResetPasswordNotification::class);

        // Laravel Octane does not register the `ReloadCommand` when the application is not running in the console.
        // We need to call this command from the `UpdateBrandSettingsController` during an HTTP request.
        if (! $this->app->runningInConsole()) {
            $this->commands([
                ReloadCommand::class,
            ]);
        }

        $this->app->scoped(StartSession::class, function ($app) {
            return new OverrideStartSession($app->make(SessionManager::class), function () use ($app) {
                return $app->make(CacheFactory::class);
            });
        });

        $this->app->scoped(
            HtmlSanitizerInterface::class,
            fn (): HtmlSanitizer => new HtmlSanitizer(
                (new HtmlSanitizerConfig())
                    ->allowSafeElements()
                    ->allowRelativeLinks()
                    ->allowRelativeMedias()
                    ->allowElement('iframe', ['src', 'width', 'height', 'frameborder', 'allowfullscreen', 'allow'])
                    ->allowAttribute('class', allowedElements: '*')
                    ->allowAttribute('data-color', allowedElements: '*')
                    ->allowAttribute('data-cols', allowedElements: '*')
                    ->allowAttribute('data-col-span', allowedElements: '*')
                    ->allowAttribute('data-from-breakpoint', allowedElements: '*')
                    ->allowAttribute('data-id', allowedElements: '*')
                    ->allowAttribute('data-type', allowedElements: '*')
                    ->allowAttribute('style', allowedElements: '*')
                    ->allowAttribute('width', allowedElements: '*')
                    ->allowAttribute('height', allowedElements: '*')
                    ->allowAttribute('data-video-embed', allowedElements: '*')
                    ->allowAttribute('data-video-type', allowedElements: '*')
                    ->allowAttribute('data-video-src', allowedElements: '*')
                    ->allowAttribute('data-video-width', allowedElements: '*')
                    ->allowAttribute('data-video-height', allowedElements: '*')
                    ->allowAttribute('wire:ignore.self', allowedElements: '*')
                    ->allowAttribute('controls', allowedElements: 'video')
                    ->allowAttribute('src', allowedElements: ['img', 'video', 'source', 'iframe'])
                    ->allowAttribute('type', allowedElements: 'source')
                    ->withMaxInputLength(500000),
            ),
        );

        $this->app->scoped(GeoPlacesClient::class, fn (): GeoPlacesClient => new GeoPlacesClient([
            'version' => '2020-11-19',
            'region' => config('services.aws_geo_places.region'),
            'credentials' => [
                'key' => config('services.aws_geo_places.key'),
                'secret' => config('services.aws_geo_places.secret'),
            ],
        ]));

        $this->loadMigrationsFrom(database_path('migrations/Legacy'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'system_user' => SystemUser::class,
            'tenant' => Tenant::class,
            'pipeline' => Pipeline::class,
            'pipeline_stage' => PipelineStage::class,
        ]);

        Feature::resolveScopeUsing(fn ($driver) => null);

        if (config('app.force_https')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }

        Queue::looping(function () {
            configureScope(function (Scope $scope): void {
                $scope->removeUser();
            });
        });

        $this->app->singleton(PermissionMigrationCreator::class, function ($app) {
            return new PermissionMigrationCreator($app['files'], $app->basePath('stubs'));
        });

        $this->app->singleton('current-commit', function ($app) {
            $commitProcess = Process::run('git log --pretty="%h" -n1 HEAD');

            if ($commitProcess->successful()) {
                return rtrim($commitProcess->output());
            }
            report($commitProcess->errorOutput());

            return null;
        });

        $this->app->singleton('current-version', function ($app) {
            $gitVersion = Process::run('git describe --tags $(git rev-list --tags --max-count=1)');

            if ($gitVersion->successful()) {
                return rtrim($gitVersion->output());
            }
            report($gitVersion->errorOutput());

            return null;
        });

        Feature::discover();

        configureScope(function (Scope $scope): void {
            $scope->setTags([
                'service' => config('app.service'),
            ]);
        });

        RateLimiter::for('notifications', function (object $job) {
            $channels = match (true) {
                $job instanceof CreateBatchedEngagement => [$job->engagementBatch->channel],
                $job instanceof SendQueuedNotifications => $job->channels,
                $job instanceof EngagementCampaignActionJob => [NotificationChannel::parse($job->actionEducatable->campaignAction->data['channel'])],
                $job instanceof EngagementEmailWorkflowActionJob => [NotificationChannel::Email],
                $job instanceof EngagementSmsWorkflowActionJob => [NotificationChannel::Sms],
                default => throw new Exception('Invalid job type'),
            };

            return collect($channels)->map(function (string|NotificationChannel $channel) {
                if ($channel instanceof NotificationChannel) {
                    $channel = $channel->value;
                }

                return match ($channel) {
                    'mail', 'email' => Limit::perSecond(14)->by('mail'),
                    default => Limit::none()->by($channel),
                };
            })
                ->toArray();
        });
    }
}
