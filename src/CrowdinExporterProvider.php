<?php

declare(strict_types=1);

namespace Pointpay\CrowdinExporter;

use Illuminate\Support\ServiceProvider;
use Pointpay\CrowdinExporter\Console\CrowdinExporter;

class CrowdinExporterProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPublishables();
    }

    /**
     * Register services.
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/crowdin-exporter.php', 'crowdin-exporter');

        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        $this->commands([
            CrowdinExporter::class,
        ]);
    }

    protected function registerPublishables(): void
    {
        $this->publishes([
            __DIR__ . '/config/crowdin-exporter.php' => config_path('crowdin-exporter.php'),
        ], 'crowdin-exporter');
    }

}
