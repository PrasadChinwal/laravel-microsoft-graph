<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Endpoints\Mail;

beforeEach(function () {
    Cache::put('microsoft_graph_access_token', 'fake-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('for()->get() returns messages collection', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/messages*' => Http::response([
            'value' => [
                ['id' => 'msg-1', 'subject' => 'Hello', 'from' => ['emailAddress' => ['address' => 'sender@test.com', 'name' => 'Sender']]],
                ['id' => 'msg-2', 'subject' => 'Re: Hello', 'from' => ['emailAddress' => ['address' => 'other@test.com', 'name' => 'Other']]],
            ],
        ], 200),
    ]);

    $messages = (new Mail)->for('john.doe@company.com')->get();

    expect($messages['value'])->toHaveCount(2)
        ->and($messages['value'][0]['subject'])->toBe('Hello');
});

test('for()->where()->get() applies filter', function () {
    Http::fake(function ($request) {
        $url = $request->url();
        expect($url)->toContain('%24filter')
            ->and(urldecode($url))->toContain("isRead ne true");

        return Http::response(['value' => []], 200);
    });

    (new Mail)
        ->for('john.doe@company.com')
        ->where('isRead', '!=', 'true')
        ->get();
});

test('for()->top()->get() sets top parameter', function () {
    Http::fake(function ($request) {
        $url = $request->url();
        expect($url)->toContain('%24top=25');

        return Http::response(['value' => []], 200);
    });

    (new Mail)
        ->for('john.doe@company.com')
        ->top(25)
        ->get();
});

test('for()->where()->get() throws exception for empty value', function () {
    (new Mail)
        ->for('john.doe@company.com')
        ->where('field', '=', '');
})->throws(\InvalidArgumentException::class, 'cannot be empty');

test('for() returns self for chaining', function () {
    $mail = new Mail();
    expect($mail->for('test@test.com'))->toBe($mail);
});

test('where() maps operators correctly', function () {
    Http::fake(function ($request) {
        $url = urldecode($request->url());
        expect($url)->toContain('age gt 18')
            ->and($url)->toContain('score ge 50')
            ->and($url)->toContain('count lt 100')
            ->and($url)->toContain('total le 200');

        return Http::response(['value' => []], 200);
    });

    (new Mail)
        ->for('user@test.com')
        ->where('age', '>', '18')
        ->where('score', '>=', '50')
        ->where('count', '<', '100')
        ->where('total', '<=', '200')
        ->get();
});
