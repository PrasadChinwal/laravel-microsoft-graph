<?php

namespace PrasadChinwal\MicrosoftGraph;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use PrasadChinwal\MicrosoftGraph\Commands\CreateGraphEventCommand;
use PrasadChinwal\MicrosoftGraph\Exceptions\ConfigurationException;

class MicrosoftGraphServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/microsoft-graph.php',
            'microsoft-graph'
        );

        $this->publishes([
            __DIR__.'/../config/microsoft-graph.php' => config_path('microsoft-graph.php'),
        ], 'microsoft-graph-config');
    }

    public function boot(): void
    {
        $this->validateConfiguration();

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

    /**
     * Validate that required configuration values are set.
     *
     * @throws ConfigurationException
     */
    protected function validateConfiguration(): void
    {
        $requiredKeys = ['tenant_id', 'client_id', 'client_secret'];

        foreach ($requiredKeys as $key) {
            $value = config("microsoft-graph.{$key}");

            if (empty($value)) {
                throw ConfigurationException::missingCredentials(strtoupper($key));
            }
        }

        // Validate timezone if set
        $timezone = config('microsoft-graph.timezone');
        if ($timezone && ! in_array($timezone, timezone_identifiers_list())) {
            throw ConfigurationException::invalid('timezone', 'Invalid timezone identifier');
        }
    }
}
