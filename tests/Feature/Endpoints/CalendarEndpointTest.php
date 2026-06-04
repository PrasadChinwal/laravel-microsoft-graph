<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Endpoints\Calendar;

beforeEach(function () {
    Cache::put('microsoft_graph_access_token', 'fake-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('for()->get() returns collection of calendars', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/calendars' => Http::response([
            'value' => [
                ['id' => 'cal-1', 'name' => 'Calendar', 'canEdit' => true, 'isDefaultCalendar' => true, 'canShare' => true, 'canViewPrivateItems' => true, 'changeKey' => 'ck-1', 'color' => 'auto', 'hexColor' => '', 'owner' => []],
                ['id' => 'cal-2', 'name' => 'Holidays', 'canEdit' => true, 'isDefaultCalendar' => false, 'canShare' => true, 'canViewPrivateItems' => true, 'changeKey' => 'ck-2', 'color' => 'auto', 'hexColor' => '', 'owner' => []],
            ],
        ], 200),
    ]);

    $calendars = (new Calendar)->for('john.doe@company.com')->get();

    expect($calendars)->toHaveCount(2)
        ->and($calendars->first()->name)->toBe('Calendar');
});

test('for()->schedule() returns schedule collection', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/calendar/getSchedule' => Http::response([
            'value' => [
                ['scheduleId' => 'john.doe@company.com', 'availabilityView' => '000'],
                ['scheduleId' => 'jane.smith@company.com', 'availabilityView' => '222'],
            ],
        ], 200),
    ]);

    $schedule = (new Calendar)->for('requester@company.com')->schedule(
        users: ['john.doe@company.com', 'jane.smith@company.com'],
        from: Carbon::now(),
        to: Carbon::now()->addDays(7),
        timezone: 'America/Chicago',
        interval: 60
    );

    expect($schedule['value'])->toHaveCount(2)
        ->and($schedule['value'][0]['scheduleId'])->toBe('john.doe@company.com');
});

test('for()->view() returns calendar events', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/calendar/calendarView*' => Http::response([
            'value' => [
                [
                    'id' => 'evt-1',
                    'subject' => 'Meeting',
                    'categories' => [],
                    'start' => ['dateTime' => '2024-01-01T10:00:00', 'timeZone' => 'UTC'],
                    'end' => ['dateTime' => '2024-01-01T11:00:00', 'timeZone' => 'UTC'],
                ],
            ],
        ], 200),
    ]);

    $events = (new Calendar)->for('john.doe@company.com')->view(
        start: '2024-01-01T00:00:00Z',
        end: '2024-01-31T00:00:00Z'
    );

    expect($events)->toHaveCount(1)
        ->and($events->first()->id)->toBe('evt-1')
        ->and($events->first()->subject)->toBe('Meeting');
});

test('for() returns self for chaining', function () {
    $calendar = new Calendar();
    expect($calendar->for('test@test.com'))->toBe($calendar);
});
