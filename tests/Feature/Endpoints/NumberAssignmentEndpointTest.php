<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Endpoints\NumberAssignmentRequest;

beforeEach(function () {
    Cache::put('microsoft_graph_access_token', 'fake-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('get() returns collection of number assignments', function () {
    Http::fake([
        'graph.microsoft.com/beta/admin/teams/telephoneNumberManagement/numberAssignments*' => Http::response([
            'value' => [
                [
                    'id' => 'num-1',
                    'telephoneNumber' => '+1234567890',
                    'assignmentStatus' => 'assigned',
                    'numberType' => 'directRouting',
                    'activationState' => 'activated',
                    'capabilities' => ['voice'],
                    'assignmentCategory' => 'user',
                ],
            ],
        ], 200),
    ]);

    $assignments = (new NumberAssignmentRequest)->get();

    expect($assignments)->toHaveCount(1)
        ->and($assignments->first()->telephoneNumber)->toBe('+1234567890')
        ->and($assignments->first()->assignmentStatus)->toBe('assigned');
});

test('where()->get() applies filter', function () {
    Http::fake(function ($request) {
        $url = urldecode($request->url());
        expect($url)->toContain("\$filter=assignmentStatus eq 'unassigned'");

        return Http::response(['value' => []], 200);
    });

    (new NumberAssignmentRequest)
        ->where(field: 'assignmentStatus', operator: '=', value: 'unassigned')
        ->get();
});

test('where()->get() with different operator', function () {
    Http::fake(function ($request) {
        $url = urldecode($request->url());
        expect($url)->toContain("\$filter=telephoneNumber ne '+000000000'");

        return Http::response(['value' => []], 200);
    });

    (new NumberAssignmentRequest)
        ->where('telephoneNumber', '!=', '+000000000')
        ->get();
});

test('where() throws exception for empty value', function () {
    (new NumberAssignmentRequest)
        ->where('field', '=', '');
})->throws(\InvalidArgumentException::class, 'cannot be empty');
