<?php

namespace Assist\LaravelAuditing;

use Illuminate\Support\ServiceProvider;
use Assist\LaravelAuditing\Contracts\Auditor;
use Assist\LaravelAuditing\Console\InstallCommand;
use Assist\LaravelAuditing\Console\AuditDriverCommand;
use Assist\LaravelAuditing\Console\AuditResolverCommand;

class AuditingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();
        $this->mergeConfigFrom(__DIR__ . '/../config/audit.php', 'audit');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            AuditDriverCommand::class,
            AuditResolverCommand::class,
            InstallCommand::class,
        ]);

        $this->app->singleton(Auditor::class, function ($app) {
            return new \Assist\LaravelAuditing\Auditor($app);
        });

        $this->app->register(AuditingEventServiceProvider::class);
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/audit.php' => config_path('audit.php'),
            ], 'config');

            if (! class_exists('CreateAuditsTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/audits.stub' => database_path(
                        sprintf('migrations/%s_create_audits_table.php', date('Y_m_d_His'))
                    ),
                ], 'migrations');
            }
        }
    }
}
