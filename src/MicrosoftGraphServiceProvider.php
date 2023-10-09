<?php

namespace PrasadChinwal\MicrosoftGraph;

use Illuminate\Support\ServiceProvider;
use PrasadChinwal\MicrosoftGraph\Commands\CreateGraphEventCommand;

class MicrosoftGraphServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->publishes([
            __DIR__.'/../config/microsoft-graph.php' => config_path('microsoft-graph.php'),
        ], 'microsoft-graph-config');
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateGraphEventCommand::class,
            ]);
        }

        $this->app->singleton('graph', function ($app) {
            return new MicrosoftGraph();
        });
    }
}
