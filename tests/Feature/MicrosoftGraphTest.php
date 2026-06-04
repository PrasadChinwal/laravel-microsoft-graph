<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

beforeEach(function () {
    config(['microsoft-graph.tenant_id' => 'test-tenant']);
    config(['microsoft-graph.client_id' => 'test-client']);
    config(['microsoft-graph.client_secret' => 'test-secret']);

    Cache::put('microsoft_graph_access_token', 'cached-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('constructor fetches token from cache when available', function () {
    $graph = new MicrosoftGraph();

    expect($graph->getAccessToken())->toBe('cached-token');
});

test('constructor fetches token from API when not cached', function () {
    Cache::forget('microsoft_graph_access_token');
    Cache::forget('microsoft_graph_token_expiry');

    Http::fake([
        'https://login.microsoftonline.com/test-tenant/oauth2/v2.0/token' => Http::response([
            'access_token' => 'api-token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ], 200),
    ]);

    $graph = new MicrosoftGraph();

    expect($graph->getAccessToken())->toBe('api-token');
});

test('clearTokenCache removes cached tokens', function () {
    $graph = new MicrosoftGraph();
    $graph->clearTokenCache();

    expect(Cache::has('microsoft_graph_access_token'))->toBeFalse()
        ->and(Cache::has('microsoft_graph_token_expiry'))->toBeFalse();
});

test('endpoint accessor methods return new instances', function () {
    $graph = new MicrosoftGraph();

    expect($graph->users())->toBeInstanceOf(\PrasadChinwal\MicrosoftGraph\Endpoints\User::class)
        ->and($graph->calendar())->toBeInstanceOf(\PrasadChinwal\MicrosoftGraph\Endpoints\Calendar::class)
        ->and($graph->event())->toBeInstanceOf(\PrasadChinwal\MicrosoftGraph\Endpoints\Event::class)
        ->and($graph->mail())->toBeInstanceOf(\PrasadChinwal\MicrosoftGraph\Endpoints\Mail::class)
        ->and($graph->outlook())->toBeInstanceOf(\PrasadChinwal\MicrosoftGraph\Endpoints\Outlook::class)
        ->and($graph->attachments())->toBeInstanceOf(\PrasadChinwal\MicrosoftGraph\Endpoints\Attachment::class)
        ->and($graph->teamConfiguration())->toBeInstanceOf(\PrasadChinwal\MicrosoftGraph\Endpoints\TeamConfigurationRequest::class)
        ->and($graph->numberAssignement())->toBeInstanceOf(\PrasadChinwal\MicrosoftGraph\Endpoints\NumberAssignmentRequest::class);
});
