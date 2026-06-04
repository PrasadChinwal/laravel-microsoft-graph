<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Endpoints\Event;

beforeEach(function () {
    Cache::put('microsoft_graph_access_token', 'fake-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('for()->get() returns collection of events', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/events*' => Http::response([
            'value' => [
                ['id' => 'evt-1', 'subject' => 'Team Sync', 'categories' => [], 'start' => ['dateTime' => '2024-01-01T10:00:00', 'timeZone' => 'UTC'], 'end' => ['dateTime' => '2024-01-01T11:00:00', 'timeZone' => 'UTC']],
                ['id' => 'evt-2', 'subject' => 'Lunch', 'categories' => [], 'start' => ['dateTime' => '2024-01-01T12:00:00', 'timeZone' => 'UTC'], 'end' => ['dateTime' => '2024-01-01T13:00:00', 'timeZone' => 'UTC']],
            ],
        ], 200),
    ]);

    $events = (new Event)->for('john.doe@company.com')->get();

    expect($events)->toHaveCount(2)
        ->and($events->first()->subject)->toBe('Team Sync');
});

test('for()->get() applies where filter', function () {
    Http::fake(function ($request) {
        $url = $request->url();
        expect($url)->toContain('%24filter')
            ->and($url)->toContain('start%2FdateTime')
            ->and($url)->toContain('ge');

        return Http::response(['value' => []], 200);
    });

    (new Event)
        ->for('john.doe@company.com')
        ->where('start/dateTime', 'ge', '2024-01-01')
        ->get();
});

test('for()->get() applies orWhere filter', function () {
    Http::fake(function ($request) {
        $url = urldecode($request->url());
        expect($url)->toContain("subject eq 'Meeting' or subject eq 'All Hands'");

        return Http::response(['value' => []], 200);
    });

    (new Event)
        ->for('john.doe@company.com')
        ->where('subject', 'eq', 'Meeting')
        ->orWhere('subject', 'eq', 'All Hands')
        ->get();
});

test('for() returns self for chaining', function () {
    $event = new Event();
    expect($event->for('test@test.com'))->toBe($event);
});
