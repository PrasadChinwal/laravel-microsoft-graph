<?php

namespace PrasadChinwal\MicrosoftGraph;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use PrasadChinwal\MicrosoftGraph\Commands\CreateGraphEventCommand;

class MicrosoftGraphServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->publishes([
            __DIR__.'/../config/microsoft-graph.php' => config_path('microsoft-graph.php'),
        ], 'microsoft-graph-config');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateGraphEventCommand::class,
            ]);
        }

        // Microsoft Graph macro for registering timezone..
        Http::macro('graph', function () {
            return Http::withHeaders([
                'Prefer' => Str::of('outlook.timezone=')
                    ->append(Str::wrap(config('microsoft-graph.timezone'), '"'))
                    ->value(),
            ]);
        });

        $this->app->singleton('graph', function ($app) {
            return new MicrosoftGraph;
        });
    }
}
