<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Endpoints\TeamConfigurationRequest;

beforeEach(function () {
    Cache::put('microsoft_graph_access_token', 'fake-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('get() returns collection of team configurations', function () {
    Http::fake([
        'graph.microsoft.com/beta/admin/teams/userConfigurations' => Http::response([
            'value' => [
                [
                    'id' => 'config-1',
                    'userPrincipalName' => 'john.doe@company.com',
                    'isEnterpriseVoiceEnabled' => true,
                    'tenantId' => 'tenant-1',
                    'telephoneNumbers' => [
                        ['telephoneNumber' => '+1234567890', 'assignmentCategory' => 'user'],
                    ],
                ],
                [
                    'id' => 'config-2',
                    'userPrincipalName' => 'jane.smith@company.com',
                    'isEnterpriseVoiceEnabled' => false,
                    'tenantId' => 'tenant-1',
                    'telephoneNumbers' => [],
                ],
            ],
        ], 200),
    ]);

    $configs = (new TeamConfigurationRequest)->get();

    expect($configs)->toHaveCount(2);
});

test('where()->get() applies filter', function () {
    Http::fake(function ($request) {
        $url = urldecode($request->url());
        expect($url)->toContain("\$filter=id eq '109c9587-bdf8-4f84-ba53-01c7ab2efa22'");

        return Http::response(['value' => []], 200);
    });

    (new TeamConfigurationRequest)
        ->where(field: 'id', operator: '=', value: '109c9587-bdf8-4f84-ba53-01c7ab2efa22')
        ->get();
});

test('hasEnterpriseVoiceEnabled helper returns correct value', function () {
    Http::fake([
        'graph.microsoft.com/beta/admin/teams/userConfigurations' => Http::response([
            'value' => [
                [
                    'id' => 'config-1',
                    'userPrincipalName' => 'john.doe@company.com',
                    'isEnterpriseVoiceEnabled' => true,
                    'tenantId' => 'tenant-1',
                    'telephoneNumbers' => [
                        ['telephoneNumber' => '+1234567890', 'assignmentCategory' => 'user'],
                    ],
                ],
            ],
        ], 200),
    ]);

    $configs = (new TeamConfigurationRequest)->get();

    expect($configs->first()->hasEnterpriseVoiceEnabled())->toBeTrue()
        ->and($configs->first()->telephoneNumbers)->toHaveCount(1)
        ->and($configs->first()->telephoneNumbers->first()->telephoneNumber)->toBe('+1234567890');
});

test('where() throws exception for empty value', function () {
    (new TeamConfigurationRequest)
        ->where('id', '=', '');
})->throws(\InvalidArgumentException::class, 'cannot be empty');
