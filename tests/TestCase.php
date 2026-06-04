<?php

namespace Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraphServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            MicrosoftGraphServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('microsoft-graph.tenant_id', 'test-tenant-id');
        $app['config']->set('microsoft-graph.client_id', 'test-client-id');
        $app['config']->set('microsoft-graph.client_secret', 'test-client-secret');
        $app['config']->set('microsoft-graph.timezone', 'UTC');

        $app['config']->set('cache.default', 'array');

        $dataConfig = require __DIR__.'/../vendor/spatie/laravel-data/config/data.php';
        $app['config']->set('data', $dataConfig);
    }

    protected function setUpFakeToken(): void
    {
        Cache::put('microsoft_graph_access_token', 'fake-access-token', 3600);
        Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
    }

    protected function fakeTokenEndpoint(): void
    {
        Http::fake([
            'https://login.microsoftonline.com/*/oauth2/v2.0/token' => Http::response([
                'access_token' => 'fake-access-token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
            ], 200),
        ]);
    }
}
