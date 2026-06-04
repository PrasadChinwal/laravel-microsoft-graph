<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Endpoints\Outlook;

beforeEach(function () {
    Cache::put('microsoft_graph_access_token', 'fake-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('for()->sendEmail() sends to single recipient', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/sendMail' => Http::response(['value' => []], 202),
    ]);

    $result = (new Outlook)
        ->for('sender@company.com')
        ->sendEmail(
            subject: 'Project Update',
            message: 'The project is progressing well...',
            to: 'recipient@company.com'
        );

    expect($result)->toBeInstanceOf(\Illuminate\Support\Collection::class);
});

test('for()->sendEmail() sends to multiple recipients', function () {
    Http::fake(function ($request) {
        $body = json_decode($request->body(), true);
        expect($body['message']['subject'])->toBe('Team Announcement')
            ->and($body['message']['toRecipients'])->toHaveCount(3)
            ->and($body['message']['toRecipients'][0]['emailAddress']['address'])->toBe('john.doe@company.com');

        return Http::response(['value' => []], 202);
    });

    (new Outlook)
        ->for('sender@company.com')
        ->sendEmail(
            subject: 'Team Announcement',
            message: 'Please review the attached document...',
            to: [
                'john.doe@company.com',
                'jane.smith@company.com',
                'team@company.com',
            ]
        );
});

test('for()->sendEmail() throws exception on non-202', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/sendMail' => Http::response([], 500),
    ]);

    (new Outlook)
        ->for('sender@company.com')
        ->sendEmail(subject: 'Test', message: 'Test', to: 'test@test.com');
})->throws(\Illuminate\Http\Client\RequestException::class);

test('for() returns self for chaining', function () {
    $outlook = new Outlook();
    expect($outlook->for('test@test.com'))->toBe($outlook);
});
